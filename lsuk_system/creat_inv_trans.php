<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include_once ('function.php'); $interp=@$_GET['interp']; $org=@$_GET['org']; $job=@$_GET['job'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 25;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<script>
function myFunction() {
	 var x = document.getElementById("interp").value;if(!x){x="<?php echo $interp; ?>";}
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("job").value;if(!z){z="<?php echo $job; ?>";}
	 window.location.href="trans_list.php" + '?interp=' + x + '&org=' + y + '&job=' + z;
	 
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
		<header><h3 class="tabs_involved" style="width:300px;">Translation Interpreter booking list</h3>
		  <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">
		    <select id="interp" onChange="myFunction()" name="interp"style="width:150px;height:25px;">
		      <?php 			
$sql_opt="SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg
JOIN interp_lang ON interpreter_reg.code=interp_lang.code
 ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["name"];
    $name_opt=$row_opt["name"];$city_opt=$row_opt["city"];$gender=$row_opt["gender"];
    $options.="<OPTION value='$code'>".$name_opt.' ('. $gender.')'.' ('. $city_opt.')';}
?>
		      <?php if(!empty($interp)){ ?>
		      <option><?php echo $interp; ?></option>
		      <?php } else{?>
		      <option value="">--Select Interpreter--</option>
		      <?php } ?>
		      <?php echo $options; ?>
		      </option>
	        </select>
		    |
  <select id="org" name="org" onChange="myFunction()" style="width:150px; height:25px;">
    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt.' ('.$code.')';}
?>
    <?php if(!empty($org)){ ?>
    <option><?php echo $org; ?></option>
    <?php } else{?>
    <option value="">--Select Company--</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select>
		    |
  <select name="job" id="job" onChange="myFunction()" style="width:150px;height:25px;">
    <?php 			
$sql_opt="SELECT lang FROM lang ORDER BY lang ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["lang"];
    $name_opt=$row_opt["lang"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
    <?php if(!empty($job)){ ?>
    <option><?php echo $job; ?></option>
    <?php } else{?>
    <option value="">--Select Language--</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select>
	      </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Source Lang</th>
    				<th>Company Name</th> 
    				<th>Booking Ref</th> 
    				<th>Asignment Date</th> 
    				<th>Contact Name</th> 
                    <th>Interpreter</th>
                    <th width="300" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='translation';
	  if(!empty($org) || !empty($interp) || !empty($job)){
	   $query="SELECT $table.*,interpreter_reg.name FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where $table.commit=0 and multInv_flag <> 1 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%'
	    LIMIT {$startpoint} , {$limit}";}
		else{$query="SELECT $table.*,interpreter_reg.name FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where $table.commit=0 and multInv_flag <> 1 
	    LIMIT {$startpoint} , {$limit}";}		
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['source']; ?></td>
   					<td><?php echo $row['orgName']; ?></td> 
    				<td><?php echo $row['orgRef']; ?></td> 
    				<td><?php echo date_format(date_create($row["asignDate"]), 'd-m-Y'); ?></td> 
    				<td><?php echo $row['orgContact']; ?></td> 
    				<td><?php echo $row['name']; ?></td>
    				<td align="center">
                    <a href="#" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 1000, 1000);">
    				  <input type="image" src="images/icn_new_article.png" title="View Order">
    				  </a>
                    <a href="#" onClick="MM_openBrWindow('trans_edit.php?edit_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                    
                     <a href="#" onClick="MM_openBrWindow('interp_assign.php?assign_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&srcLang=<?php echo $row['source']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				    <input type="image" src="images/icn_add_user.png" title="Edit Assign Interpreter">
    				    <?php if($_SESSION['prv']=='Management'){?>
  				     </a>
                    
                    <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_trash.png" title="Trash"><?php } ?>
                    
                    </a>
              <?php if(@$_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>      
                   <?php if($row['total_charges_interp']==NULL || $row['total_charges_interp']==0 || $_SESSION['prv']=='Management'){ ?>  <a href="#" onClick="MM_openBrWindow('trans_update_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/Update.png" title="Update Expanses"></a><?php } ?>
              <?php if($row['multInv_flag']==0){ ?>        
                     <a href="#" onClick="MM_openBrWindow('trans_invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/invoice.png" title="Invoice">
                     </a> <?php } ?>
                     
                       <?php if($row['orderCancelatoin']==0){ ?>
  <a href="#" onClick="MM_openBrWindow('email_cancel.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
  <?php }else{ ?>
  <a href="#" onClick="MM_openBrWindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_alert_error.png" title="Order Canceled"></a>
  
  <?php } ?>
                    
                    
                  <?php if($row['total_charges_comp']==NULL || $row['total_charges_comp']==0 || $_SESSION['prv']=='Management'){ ?>  <a href="#" onClick="MM_openBrWindow('co_trans_update_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/company-icon.jpg" title="Comp Update Expanses"></a><?php } ?>
                 <?php if($_SESSION['prv']=='Management'){?>   <a href="#" onClick="MM_openBrWindow('comp_earning_trans.php?view_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/earning.png" title="Earning"></a><?php } ?>
					<!--<a href="#" onClick="MM_openBrWindow('paid_amount.php?invoice_No=<?php //echo $row['invoiceNo']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/paid.png" title="Paid to Interpreter"></a>-->
                    
 
                    </td> 
				</tr> 
                <?php } ?><?php } ?>
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