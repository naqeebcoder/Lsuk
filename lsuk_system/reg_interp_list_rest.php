<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include_once ('function.php');include'class.php';$name=@$_GET['name']; $srchgender=@$_GET['srchgender']; $city=@$_GET['city'];$lang=@$_GET['lang'];$adLang=@$_GET['adLang'];$active=@$_GET['active'];$act_id=@$_GET['act_id'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">

<script>
<?php if($adLang=='adLang'  && $lang==''){ ?>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("srchgender").value;if(!y){y="<?php echo $srchgender; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 var p = document.getElementById("lang").value;if(!p){p="<?php echo $lang; ?>";}
	 window.location.href="reg_interp_list_rest.php?adLang=adLang" + '&name=' + x + '&srchgender=' + y + '&city=' + z + '&lang=' + p;	 
}<?php }else{ ?>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("srchgender").value;if(!y){y="<?php echo $srchgender; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 var p = document.getElementById("lang").value;if(!p){p="<?php echo $lang; ?>";}
	 window.location.href="reg_interp_list_rest.php" + '?name=' + x + '&srchgender=' + y + '&city=' + z + '&lang=' + p;	 
}
<?php } ?>
</script>

<?php include 'header.php'; ?>
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header><h3 class="tabs_involved" style="width:230px;"><a href="<?php echo basename(__FILE__);?>">Registered Interpreters list</a></h3>
         <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">
<a href="reg_interp_list_rest.php?adLang=adLang" style="color:#F00; font-weight:bold;">Add Lang</a> |
        
        <select id="name" onChange="myFunction()" name="name"style="width:120px;height:25px;">
		      <?php 			
$sql_opt="SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg WHERE interpreter_reg.deleted_flag=1 ORDER BY interpreter_reg.name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["name"];
    $name_opt=$row_opt["name"];$city_opt=$row_opt["city"];$gender=$row_opt["gender"];
    $options.="<OPTION value='$code'>".$name_opt.' ('. $gender.')'.' ('. $city_opt.')';}
?>
		      <?php if(!empty($name)){ ?>
		      <option><?php echo $name; ?></option>
		      <?php } else{?>
		      <option value="">--Select Interpreter--</option>
		      <?php } ?>
		      <?php echo $options; ?>
		      </option>
	        </select>
		    |
  <select name="lang" id="lang" onChange="myFunction()" style="width:100px;height:25px;">
    <?php 			
$sql_opt="SELECT lang FROM lang ORDER BY lang ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["lang"];
    $name_opt=$row_opt["lang"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
    <?php if(!empty($lang)){ ?>
    <option><?php echo $lang; ?></option>
    <?php } else{?>
    <option value="">--Select Language--</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select> |
        <select name="srchgender" id="srchgender" onChange="myFunction()" style="width:120px;height:25px;">
        		      <?php if(!empty($srchgender)){ ?>
		      <option><?php echo $srchgender; ?></option>
		      <?php } else{?>
		      <option value="">--Select Gender--</option>
		      <?php } ?>
                <option>Male</option>
                <option>Female</option>
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
                  </select></div>

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
                    <th>Submitted By</th>
                    <th style="color:#F00">Deleted by</th> 
    				<th width="230" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='interpreter_reg';
	  if($adLang='adLang' && $lang==''){ $query="SELECT distinct $table.* FROM $table 		
	   	where $table.deleted_flag=1 and  name like '$name%' and gender like '$srchgender%' and city like '$city%'	  
	  	 LIMIT {$startpoint} , {$limit}";}
		else{	  
	  $query="SELECT distinct $table.* FROM $table
		JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
		
	   	where $table.deleted_flag=1 and  interp_lang.lang like '$lang%' and name like '$name%' and gender like '$srchgender%' and city like '$city%'	  
	  	group by email  LIMIT {$startpoint} , {$limit}";	}
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){ 
			$dob=$row['dob']; $bnakName=$row['bnakName']; $acName=$row['acName'];  $acntCode=$row['acntCode'];  $acNo=$row['acNo'];?>
			            
				<tr> 
				  <td id="emtpyclr"><?php if(empty($dob) || $dob== '0000-00-00' || empty($bnakName) || empty($acName) || empty($acntCode) || empty($acNo)){ ?> <span style="color:#F00"><?php echo $row['name']; ?></span><?php } else{echo $row['name'];}?></td>
				  <td><?php echo $row['gender']; ?></td> 
   					<td><?php echo $row['interp']; ?></td> 
    				<td><?php echo $row['telep']; ?></td> 
    				<td><?php echo $row['trans']; ?></td> 
                    <td><?php echo $row['city']; ?></td> 
    				<td><?php echo $row['contactNo']; ?></td>
    				<td><?php echo $row['email']; ?></td>    	
    				<td><?php echo $row['sbmtd_by']; ?></td>  
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
			<?php if($active==0 && !empty($act_id)){$acttObj->editFun($table,$act_id,'active',1); ?><script>window.location.href="<?php echo basename(__FILE__);?>"</script><?php }if($active==1 && !empty($act_id)){$acttObj->editFun($table,$act_id,'active',0); ?><script>window.location.href="<?php echo basename(__FILE__);?>"</script><?php } ?>
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>
