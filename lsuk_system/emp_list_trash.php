<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$name=@$_GET['name']; $gender=@$_GET['gender']; $city=@$_GET['city'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">

<script>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("gender").value;if(!y){y="<?php echo $gender; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?name=' + x + '&gender=' + y + '&city=' + z;
	 
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
		<header><h3 class="tabs_involved" style="width:230px;"><a href="<?php echo basename(__FILE__);?>">Registered Employees list</a></h3>
         <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">
   <?php if($_SESSION['prv']=='Management'){ ?>
        <select id="name" onChange="myFunction()" name="name"style="width:130px;height:25px;">
		      <?php 			
$sql_opt="SELECT name FROM emp
 ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["name"];
    $name_opt=$row_opt["name"];$city_opt=$row_opt["city"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($name)){ ?>
		      <option><?php echo $name; ?></option>
		      <?php } else{?>
		      <option value="">--Select Interpreter--</option>
		      <?php } ?>
		      <?php echo $options; ?>
		      </option>
	        </select> |
         <select id="gender" onChange="myFunction()" name="gender"style="width:130px;height:25px;">
		       <?php if(!empty($gender)){ ?>
		      <option><?php echo $gender; ?></option>
		      <?php } else{?>
		      <option value="">--Select Gender--</option>
		     
              <option>Male</option>
              <option>Female</option> <?php } ?>
	        </select> |
        <select name="city" id="city" onChange="myFunction()" style="width:120px;height:25px;">
        		      <?php if(!empty($city)){ ?>
		      <option><?php echo $city; ?></option>
		      <?php } else{?>
		      <option value="">--Select City--</option>
		      <?php } ?>
                    <optgroup label="England">
                      <option>Bath</option>
                      <option>Birmingham</option>
		      		  <option>Bradford</option>
                      <option>Bridgwater</option>
                      <option>Bristol</option>
                      <option>Buckinghamshire</option>
                      <option>Cambridge</option>
                      <option>Canterbury</option>
                      <option>Carlisle</option>
                      <option>Chippenham</option>
                      <option>Cheltenham</option>
                      <option>Cheshire</option>
                      <option>Coventry</option>
                      <option>Derby</option>
                      <option>Dorset</option>
                      <option>Exeter</option>
                      <option>Frome</option>
                      <option>Gloucester</option>
                      <option>Hereford</option>
                      <option>Leeds</option>
                      <option>Leicester</option>
                      <option>Liverpool</option>
                      <option>London</option>
                      <option>Manchester</option>
                      <option>Newcastle</option>
                      <option>Northampton</option>
                      <option>Norwich</option>
                      <option>Nottingham</option>
                      <option>Oxford</option>
                      <option>Plymouth</option>
                      <option>Pool</option>
                      <option>Portsmouth</option>
                      <option>Salford</option>
                      <option>Shefield</option>
                      <option>Somerset</option>
                      <option>Southampton</option>
                      <option>Swindon</option>
                      <option>Suffolk</option>
                      <option>Surrey</option>
                      <option>Taunton</option>
                      <option>Trowbridge</option>
                      <option>Truro</option>
                      <option>Warwick</option>
                      <option>Wiltshire</option>
                      <option>Winchester</option>
                      <option>Wells</option>
                      <option>Weston Super Mare</option>
                      <option>Worcester</option>
                      <option>Wolverhampton</option>
                      <option>York</option>           
                    </optgroup>
                    <optgroup label="Scotland">
                      <option>Dundee</option>
                      <option>Edinburgh</option>
                      <option>Glasgow</option>
                    </optgroup>
                    <optgroup label="Wales">
                      <option>Cardiff</option>
                      <option>Newport</option>
                      <option>Swansea</option>
                    </optgroup>                   
                  </select><?php } ?></div>

		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Name</th>
				  <th>Designation</th>
   				  <th>Job Type</th> 
				  <th>Gender</th> 
   				  <th>Contact No</th> 
                    <th>City</th> 
                    <th style="color:#F00">Deleted by</th> 
    				<th width="210" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='emp';  $pasport=$_SESSION['pasport'];
	  if($_SESSION['prv']=='Management'){		
	 	$query="SELECT * FROM $table 
	   where $table.deleted_flag=1 and name like '$name%' and gender like '$gender%' and city like'$city%'
	   LIMIT {$startpoint} , {$limit}";	}
	   else{ $query="SELECT * FROM $table 
	   where $table.deleted_flag=1 and passp = '$pasport' and name like '$name%' and gender like '$gender%' and city like'$city%'
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