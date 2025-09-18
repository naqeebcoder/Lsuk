<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';?>
<!doctype html>
<html lang="en">


<?php include 'header.php'; ?>
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_3_quarter">
		<header><h3 class="tabs_involved">Advance Search</h3>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<form action="interp_search.php" method="get">
            <table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
			  </thead> 
			<tbody> 
            
  
				<tr>
				  <td align="right"><strong>Company Name</strong></td>
				  <td><select id="orgName" name="orgName" style="width:190px;">
				    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
				    <option value="0">--Select--</option>
				    <?php echo $options; ?>
				    </option>
			      </select></td>
			  </tr>
				<tr>
				  <td align="right"><strong><span class="optional">City</span></strong></td>
				  <td><select name="assignCity" style="width:190px;">
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
			      </select></td>
			  </tr>
				<tr>
				  <td align="right"><strong>Date</strong></td>
				  <td><input name="dated" type="date" style="border:1px solid #CCC;width:190px;" value="" required='' /></td>
			  </tr>
				<tr>
				  <td>&nbsp;</td>
				  <td><div><input type="submit" name="submit" value="Submit &raquo;"/></div></td>
			  </tr> 
               
                </tbody>
            </table>                
			</form>
		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>