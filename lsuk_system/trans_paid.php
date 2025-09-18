<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$asignDate=@$_GET['asignDate']; $interp=@$_GET['interp']; $org=@$_GET['org']; $job=@$_GET['job'];$our=@$_GET['our'];$ur=@$_GET['ur'];$inov=@$_GET['inov'];
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
	 var w = document.getElementById("asignDate").value;if(!w){w="<?php echo $asignDate; ?>";}
	 var x = document.getElementById("interp").value;if(!x){x="<?php echo $interp; ?>";}
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("job").value;if(!z){z="<?php echo $job; ?>";}
	 window.location.href="trans_paid.php" + '?interp=' + x + '&org=' + y + '&job=' + z + '&asignDate=' + w + '&our=' + p+ '&ur=' + q + '&inov=' + o;
	 
}
</script>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="css/bootstrap.css">
<style>.tablesorter thead tr{background: none !important;}</style>
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header>
		  <h3 class="tabs_involved" style="width:300px;"><a href="<?php echo basename(__FILE__);?>">Trans paid List</a> Sum Received: £(<span id="lblTotalPrice"></span>)</h3>
		  <div align="right" style=" width:75%; float:right;margin-top:3px;">
          <input type="text" name="inov" id="inov" style="width:80px;height:19px;" placeholder="Invoice #"onChange="myFunction()" value="<?php echo $inov; ?>"/>
          <input type="text" name="our" id="our" style="width:80px;height:19px;" placeholder="Our Ref"onChange="myFunction()"value="<?php echo $our; ?>"/>
          <input type="text" name="ur" id="ur" style="width:80px;height:19px;"placeholder="Your Ref"onChange="myFunction()"value="<?php echo $ur; ?>"/>
		    <select id="interp" onChange="myFunction()" name="interp"style="width:100px;height:25px;">
		      <?php 			
$sql_opt="SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg
JOIN interp_lang ON interpreter_reg.code=interp_lang.code
JOIN translation ON translation.intrpName=interpreter_reg.id
where (orgName like '%$_words%') and translation.multInv_flag=0 and translation.commit=1 and (total_charges_comp + (total_charges_comp*0.2) <= rAmount)
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
JOIN translation ON translation.orgName=comp_reg.abrv
where translation.multInv_flag=0 and translation.commit=0 
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
JOIN translation ON translation.source=lang.lang
where translation.multInv_flag=0 and translation.commit=0
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
  <input type="date" name="ssignDate" id="asignDate" placeholder='' style="width:100px;border-radius: 5px;" onChange="myFunction()" value="<?php echo $asignDate; ?>"/>
	      </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead class="bg-primary">
                            <tr>
                	<th>Source Lang</th>
    				<th>Company Name</th> 
    				<th>Booking Ref</th> 
    				<th>Asignment Date</th> 
    				<th>Contact Name</th> 
                    <th>Interpreter</th> 
                    <th>Enterd By</th> 
                    <th>Intrp Hrz</th> 
                    <th>comp Hrz</th> 
                    <th>Job Note</th> 
                    <th>Invoice Amount</th>
                    <th>Dated</th>
    				        <th>Remaining</th> 
                    <th width="145" align="center">Actions</th> 
   				</tr> 
			</thead> 
			<tbody style="font-size:11px;"> 
            <?php $arr = explode(',', $org);$_words = implode("' OR orgName like '", $arr);$arr_intrp = explode(',', $interp);$_words_intrp = implode("' OR name like '", $arr_intrp); ?>
			<?php 
			$table='translation';
			$counter=0;
	 
	    $query="SELECT $table.*,interpreter_reg.name FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where $table.deleted_flag = 0
		 and $table.order_cancel_flag=0
		 and $table.asignDate like '$asignDate%'
		 and  $table.commit=1
		 and (total_charges_comp + total_charges_comp*0.2 <= rAmount)
		 and (total_charges_comp <= rAmount)
		 and source like '%$job%' 
		 and interpreter_reg.name like '%$interp%' 
		 and $table.orgName like '%$org%' 
		 and $table.nameRef like '%$our%' 
		 and $table.orgRef like '%$ur%' 
		 and $table.invoiceNo like '%$inov%'
	   order by asignDate LIMIT {$startpoint} , {$limit}";
			$result = mysqli_query($con,$query);
			if($result)
			{
			while($row = mysqli_fetch_array($result)){
				$counter++;
				?>    
				<tr>
                	 <td><?php echo $row['source']; ?></td>
   					<td><?php if($row['C_numberUnit']==0){ ?><span style="color:#F00" title="Comp Units: <?php echo $row['C_numberUnit']; ?>"><?php echo $row['orgName']; ?></span><?php }else{ echo $row['orgName']; }?></td> 
    				<td><?php echo $row['orgRef']; ?></td> 
    				<td><?php echo date_format(date_create($row["asignDate"]), 'd-m-Y'); ?></td> 
    				<td><?php echo $row['orgContact']; ?></td> 
    				<td><?php if($row['numberUnit']==0){ ?><span style="color:#F00" title="Interp Units: <?php echo $row['numberUnit']; ?>"><?php echo $row['name']; ?></span><?php }else{ echo $row['name']; }?></td> 
    				<td><?php echo $row['submited'].'('.$misc->dated($row['dated']).')'; ?></td> 
    				<td><?php echo $row['hrsubmited'].'('.$misc->dated($row['interp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['comp_hrsubmited'].'('.$misc->dated($row['comp_hr_date']).')'; ?></td>  
    				<td><?php echo $row['snote']; ?></td>  

					<?php 
					//##gotcreditnote
						$totinvnow=$row['total_charges_comp']* $row["cur_vat"] + 
							$row['total_charges_comp'] + $row['C_otherCharg'];
						$gotcreditnote=false;
						if (isset($row['credit_note']) && $row['credit_note']!="")
						{
							$totinvnow=0;
							$gotcreditnote=true;
						}
					?>
					

    				<td><?php echo $totinvnow; ?></td>

    				<td class="totalprice"><?php echo $row['rAmount']; ?></td>
                    <td><?php echo $row['rDate']; ?></td> 
    				<td align="center"><a href="#" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 1000, 1000);">
    				  <input type="image" src="images/icn_new_article.png" title="View Order"></a>
                      
                       <a href="#" onClick="MM_openBrWindow('receive_amount.php?row_id=<?php echo $row['id']; ?>&table=<?php echo $table ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				    <input type="image" src="images/Cash.png" title="Payment Received">
                      
                     <a href="#" onClick="MM_openBrWindow('trans_invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/invoice.png" title="Invoice"></a>



<?php 
if ($gotcreditnote)
{ 
	?>
    <a href="#" onClick="MM_openBrWindow('credit_trans.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
		<input style="background-color:red;" type="image" src="images/icn_categories.png" title="Credit Note">
	</a>
	<?php    
}
else
{
	?>
	<input type="image" src="images/icn_categories.png" title="No Credit Note">
	<?php    
}
?>



                      
              <a href="#" onClick="MM_openBrWindow('un_commit.php?com_id=<?php echo $row['id']; ?>&un_comit_paid=1&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=250')">
    				  <input type="image" src="images/icn_jump_back.png" title="Un-commit 'Received payment become 0'">
    				  </a>        
                      
                      </td> 
   				</tr> 
								<?php } 
								
							}?>
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
<script>function TotalPriceCalc() {
    var total = 0;
    $(".totalprice").each(function() {
        total += parseFloat($(this).text());
    });
    $("#lblTotalPrice").text(Math.round(total));
    //alert(Math.round(total));
} TotalPriceCalc(); </script>
</html>