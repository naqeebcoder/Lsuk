<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$assignDate=@$_GET['assignDate']; $interp=@$_GET['interp']; $org=@$_GET['org']; $job=@$_GET['job'];$our=@$_GET['our'];$ur=@$_GET['ur'];$inov=@$_GET['inov'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<script>
function myFunction() {
	 var o = document.getElementById("inov").value;if(!o){o="<?php echo $inov; ?>";}
	 var p = document.getElementById("our").value;if(!p){p="<?php echo $our; ?>";}
	 var q = document.getElementById("ur").value;if(!q){q="<?php echo $ur; ?>";}
	 var w = document.getElementById("assignDate").value;if(!w){w="<?php echo $assignDate; ?>";}
	 var x = document.getElementById("interp").value;if(!x){x="<?php echo $interp; ?>";}
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("job").value;if(!z){z="<?php echo $job; ?>";}
	 window.location.href="telep_list.php" + '?interp=' + x + '&org=' + y + '&job=' + z + '&assignDate=' + w + '&our=' + p+ '&ur=' + q + '&inov=' + o;
	 
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
		<header>
		  <h3 class="tabs_involved" style="width:200px;"><a href="<?php echo basename(__FILE__);?>">Telep Interp Booking  list</a></h3>
	      <div align="right" style=" width:75%; float:right;margin-top:3px;">
          <input type="text" name="inov" id="inov" style="width:80px;height:19px;" placeholder="Invoice #"onChange="myFunction()" value="<?php echo $inov; ?>"/>
          <input type="text" name="our" id="our" style="width:80px;height:19px;" placeholder="Our Ref"onChange="myFunction()"value="<?php echo $our; ?>"/>
          <input type="text" name="ur" id="ur" style="width:80px;height:19px;"placeholder="Your Ref"onChange="myFunction()"value="<?php echo $ur; ?>"/>
		    <select id="interp" onChange="myFunction()" name="interp"style="width:100px;height:25px;">
		      <?php 			
$sql_opt="SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg
JOIN interp_lang ON interpreter_reg.code=interp_lang.code
JOIN interpreter ON interpreter.intrpName=interpreter_reg.id
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
  <select id="org" name="org" onChange="myFunction()" style="width:100px; height:25px;">
    <?php 			
$sql_opt="SELECT distinct comp_reg.name,comp_reg.abrv FROM comp_reg
JOIN interpreter ON interpreter.orgName=comp_reg.abrv
 ORDER BY comp_reg.name ASC";
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
  <select name="job" id="job" onChange="myFunction()" style="width:100px;height:25px;">
    <?php 			
$sql_opt="SELECT distinct lang FROM lang
JOIN interpreter ON interpreter.source=lang.lang
 ORDER BY lang ASC";
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
		    |
  <input type="date" name="assignDate" id="assignDate" placeholder='' style="width:100px;border-radius: 5px;" onChange="myFunction()" value="<?php echo $assignDate; ?>"/>
	      </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Source Lang</th>
    				<th>Company Name</th> 
    				<th>Interpreter</th> 
    				<th>Contact Name</th> 
                    <th>Assign-Date</th>
                    <th>Assign-Time</th>
                    <th>Enterd By</th> 
                    <th>Intrp Hrz</th> 
                    <th>comp Hrz</th> 
                    <th>Job Note</th> 
                    <th>Booking Type</th> 
    				<th width="320" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='telephone';
	  
	  switch($_SESSION['prv']){
		case 'Management':
	
		$query="SELECT $table.*,interpreter_reg.name FROM $table
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id	   
		where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and $table.assignDate like '$assignDate%' and $table.multInv_flag=0 and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		 order by assignDate LIMIT {$startpoint} , {$limit}";	
		
		break; 
		case 'Finance':
		
		$query="SELECT $table.*,interpreter_reg.name FROM $table
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id	   
		where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and $table.hoursWorkd<>0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' order by assignDate and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		LIMIT {$startpoint} , {$limit}";
		break; 
		case 'Operator':
		
		$query="SELECT $table.*,interpreter_reg.name FROM $table
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id	   
		where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and $table.hoursWorkd=0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' order by assignDate and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		LIMIT {$startpoint} , {$limit}";	
		break;  
	  }
	  
	  
		
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr title="<?php echo "Tracking ID:". $row['id']; ?>">
				  <td><?php echo $row['source']; ?></td>
   					<td><?php if($row['C_hoursWorkd']==0){ ?><span style="color:#F00" title="Comp Hours: <?php echo $row['C_hoursWorkd']; ?>"><?php echo $row['orgName']; ?></span><?php }else{ echo $row['orgName']; }?></td>  
    				<td><?php if($row['hoursWorkd']==0){ ?><span style="color:#F00" title="Interp Hours: <?php echo $row['hoursWorkd']; ?>"><?php echo $row['name']; ?></span><?php }else{ echo $row['name']; }?></td> 
    				<td><?php echo $row['orgContact']; ?></td> 
    				<td><?php echo date_format(date_create($row['assignDate']), 'd-m-Y'); ?></td>
    				<td><?php echo $row['assignTime']; ?></td>  
    				<td><?php echo $row['submited'].'('.$misc->dated($row['dated']).')'; ?></td> 
    				<td><?php echo $row['hrsubmited'].'('.$misc->dated($row['interp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['comp_hrsubmited'].'('.$misc->dated($row['comp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['snote']; ?></td> 
    				<td><?php echo $row['bookinType']; ?></td>  
    				<td align="center">
                    <a href="#" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 1000, 1000);">
    				  <input type="image" src="images/icn_new_article.png" title="View Order">
    				  </a>
                      
                      <a href="#" onClick="MM_openBrWindow('telep_edit.php?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/commit.png" title="Create Duplicate"></a>
                      
 <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){?>                   
                    <?php if($_SESSION['prv']=='Management'){?>
                    <a href="#" onClick="MM_openBrWindow('telep_edit.php?edit_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                    
                    <a href="#" onClick="MM_openBrWindow('interp_assign.php?assign_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&srcLang=<?php echo $row['source']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				    <input type="image" src="images/icn_add_user.png" title="Edit Assign Interpreter"></a>
                       <?php } ?>
                        
    				    <?php }if($_SESSION['prv']=='Management'){?>
  				     <a href="#" onClick="MM_openBrWindow('email_emend.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&col=intrpName','_blank','scrollbars=yes,resizable=yes,width=400,height=200')">
                    <input type="image" src="images/icn_jump_back.png" title="Go Home Screen"></a>
                       
                    
                    <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_trash.png" title="Trash"> </a>
                    
                   
                    
                    <?php }if($_SESSION['prv']=='Management' || $_SESSION['prv']!='Finance'){ ?> <a href="#" onClick="MM_openBrWindow('telep_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/Update.png" title="Update Expenses"></a>
                    
                     <?php }if(($row['hoursWorkd']!=0)  && $_SESSION['prv']=='Finance'){ ?> <a href="#" onClick="MM_openBrWindow('telep_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/Update.png" title="Update Expenses"></a>
                     
              <?php }if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'){?>       
                     <a href="#" onClick="MM_openBrWindow('telep_invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/invoice.png" title="Invoice"></a>

<a href="#" onClick="MM_openBrWindow('comp_telep_credit_note.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_settings.png".png" title="Make Credit Note"></a>


<?php if($row['credit_note']){ ?>
<a href="#" onClick="MM_openBrWindow('credit_telep.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_categories.png" title="Credit Note"></a>
                     
                     <?php }}if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
     <?php if($row['orderCancelatoin']==0){ ?>
  <a href="#" onClick="MM_openBrWindow('email_cancel.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
  <?php }else{ ?>
  <a href="#" onClick="MM_openBrWindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_alert_error.png" title="Order Canceled"></a>
  
  <?php }} ?>
        
                    
                   <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
                   
                    <a href="#" onClick="MM_openBrWindow('co_telep_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/company-icon.jpg" title="Comp Update Expenses"> </a>
                    <a href="#" onClick="MM_openBrWindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&orgName=<?php echo $row['orgName']; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=300')"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
                   
                    <a href="#" onClick="MM_openBrWindow('comp_earning_telep.php?view_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/earning.png" title="Earning"></a>
					<!--<a href="#" onClick="MM_openBrWindow('paid_amount.php?invoice_No=<?php //echo $row['invoiceNo']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/paid.png" title="Paid to Interpreter"></a>-->
                    

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