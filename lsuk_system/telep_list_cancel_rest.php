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
	 window.location.href="<?php echo basename(__FILE__);?>" + '?interp=' + x + '&org=' + y + '&job=' + z + '&assignDate=' + w + '&our=' + p+ '&ur=' + q + '&inov=' + o;
	 
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
		  <h3 class="tabs_involved" style="width:200px;"><a href="<?php echo basename(__FILE__);?>">Telephone Cancelled Orders list</a></h3>
	      <div align="right" style=" width:75%; float:right;margin-top:3px;">
          <input type="text" name="inov" id="inov" style="width:80px;height:19px;" placeholder="Invoice #"onChange="myFunction()" value="<?php echo $inov; ?>"/>
          <input type="text" name="our" id="our" style="width:80px;height:19px;" placeholder="Our Ref"onChange="myFunction()"value="<?php echo $our; ?>"/>
          <input type="text" name="ur" id="ur" style="width:80px;height:19px;"placeholder="Your Ref"onChange="myFunction()"value="<?php echo $ur; ?>"/>
		    <select id="interp" onChange="myFunction()" name="interp"style="width:100px;height:25px;">
		      <?php 			
$sql_opt="SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg
JOIN interp_lang ON interpreter_reg.code=interp_lang.code
JOIN telephone ON telephone.intrpName=interpreter_reg.id
where telephone.multInv_flag=0 and telephone.commit=0
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
JOIN telephone ON telephone.orgName=comp_reg.abrv
where telephone.multInv_flag=0 and telephone.commit=0
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
JOIN telephone ON telephone.source=lang.lang
where telephone.multInv_flag=0 and telephone.commit=0
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
    				<th>Contact Name</th>
    				<th>Allocated By</th> 
                    <th>Assign-Date</th>
                    <th>Assign-Time</th>
                    <th>Enterd By</th> 
                    <th style="color:#F00">Deleted by</th> 
                    <th>Job Note</th> 
                    <th>Booking Type</th> 
    				<th width="320" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='telephone';
	  
	  switch($_SESSION['prv']){
		case 'Management':
	
		$query="SELECT $table.* FROM $table	   
		where  $table.deleted_flag=1 and $table.assignDate like '$assignDate%' and $table.multInv_flag=0   and $table.commit=0 and source like '%$job%' and $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		 order by assignDate LIMIT {$startpoint} , {$limit}";	
		
		break; 
		case 'Finance':
		
		$query="SELECT $table.* FROM $table	   
		where  $table.deleted_flag=1 and  $table.hoursWorkd<>0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and $table.orgName like '%$org%' order by assignDate and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		LIMIT {$startpoint} , {$limit}";
		break; 
		case 'Operator':
		
		$query="SELECT $table.* FROM $table	   
		where  $table.deleted_flag=1 and  orderCancelatoin=0 and $table.hoursWorkd=0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and $table.orgName like '%$org%' order by assignDate and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		LIMIT {$startpoint} , {$limit}";	
		break;  
	  }
	  
	  
		
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['source']; ?></td>
   					<td><?php if($row['C_hoursWorkd']==0){ ?><span style="color:#F00" title="Comp Hours: <?php echo $row['C_hoursWorkd']; ?>"><?php echo $row['orgName']; ?></span><?php }else{ echo $row['orgName']; }?></td>  
    				<td><?php echo $row['orgContact']; ?></td>
    				<td><?php echo $row['aloct_by'].'('.$misc->dated($row['aloct_date']).')'; ?></td> 
    				<td><?php echo date_format(date_create($row['assignDate']), 'd-m-Y'); ?></td>
    				<td><?php echo $row['assignTime']; ?></td>  
    				<td><?php echo $row['submited'].'('.$misc->dated($row['dated']).')'; ?></td> 
    				<td  style="color:#F00"><?php echo $row['deleted_by'].'('.$misc->dated($row['deleted_date']).')'; ?></td> 
    				<td><?php echo $row['snote']; ?></td> 
    				<td><?php echo $row['bookinType']; ?></td>  
    				<td align="center">

<?php if($_SESSION['prv']=='Management'){?>
<a href="#" onClick="MM_openBrWindow('job_restore.php?rest_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=200')"><input type="image" src="images/icn_jump_back.png" title="Restore"></a>

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