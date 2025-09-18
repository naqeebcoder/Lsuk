<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include_once ('function.php');include'class.php';$code_qs=@$_GET['code_qs']; $name=@$_GET['name']; $srchgender=@$_GET['srchgender']; $city=@$_GET['city'];$lang=@$_GET['lang'];$adLang=@$_GET['adLang'];$active=@$_GET['active'];$act_id=@$_GET['act_id'];
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
	 window.location.href="reg_interp_list.php?adLang=adLang" + '&name=' + x + '&srchgender=' + y + '&city=' + z + '&lang=' + p;	 
}<?php }else{ ?>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("srchgender").value;if(!y){y="<?php echo $srchgender; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 var p = document.getElementById("lang").value;if(!p){p="<?php echo $lang; ?>";}
	 window.location.href="reg_interp_list.php" + '?name=' + x + '&srchgender=' + y + '&city=' + z + '&lang=' + p;	 
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
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Feedback of <span style="color:#F00"><?php echo $name;  ?>   
             <label class="optional" style="color:#069; font-size:15px"> Calculated Stars: 
              </label>
             <?php 
			
       	$query="SELECT (sum(punctuality) + sum(appearance) + sum(professionalism) + sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + sum(communication)) as sm,COUNT(interp_assess.id) as diviser FROM interp_assess
		JOIN interpreter_reg ON interp_assess.interpName=interpreter_reg.code	 
		
	   	where interp_assess.interpName='$code_qs'";	
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){$diviser=$row['diviser'];if($diviser<=0){$diviser=1;} $assess_num=$row['sm']*100/($diviser*120); }
			//echo $assess_num;
			if($assess_num<=5){echo 'Zero Star';}
			if($assess_num>6 && $assess_num<=20){echo '* ';}
			if($assess_num>20 && $assess_num<=40){echo '** ';}
			if($assess_num>40 && $assess_num<=60){echo '*** ';}
			if($assess_num>60 && $assess_num<=80){echo '**** ';}
			if($assess_num>80 && $assess_num<=100){echo '***** ';}
			?>
           </span></a></h3>


		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Organization</th>
				  <th>Feedback By</th>
				  <th>Positive Remarks</th> 
    				  <th>Negative Remarks</th> 
   				  <th>Submitted By</th> 
   				  <th>Dated</th> 
    				<th width="230" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='interp_assess';
	  	  	$query="SELECT * from $table	 
		
	   	where interp_assess.interpName='$code_qs'  LIMIT {$startpoint} , {$limit}";	
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){ ?>
			            
				<tr title="Feedback for Invoice No: <?php echo $row['order_id']==0?'Nil':$row['order_id']; ?>"> 
				 
				<td><?php echo $row['orgName']; ?></td> 
   				<td><?php echo $row['p_feedbackby']; ?></td> 
   				<td><?php echo $row['p_reason']; ?></td> 
    				<td><?php echo $row['n_reason']; ?></td> 
    				<td><?php echo $row['submittedBy']; ?></td> 
                    <td><?php echo $row['dated']; ?></td>  				
    				<td align="center">
                                           <?php if($_SESSION['prv']=='Management'){?>
                     <a href="#" onClick="MM_openBrWindow('interp_assessment_edit.php?edit_id=<?php echo $row['id']; ?>&code_qs=<?php echo $row['interpName']; ?>&name=<?php echo $name; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                    
<a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=200')"><input type="image" src="images/icn_trash.png" title="Trash"></a> 
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
