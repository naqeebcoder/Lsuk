<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include_once ('function.php');include'class.php';$name=@$_GET['name']; $srchgender=@$_GET['srchgender']; $city=@$_GET['city'];$lang=@$_GET['lang'];$adLang=@$_GET['adLang'];$active=@$_GET['active'];$act_id=@$_GET['act_id'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Registered Interpreters Edited History</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>    
	
	<section id="main" class="column">
				<article class="module width_full">
		<header><h3 class="tabs_involved"><a href="<?php echo basename(__FILE__);?>">Registered Interpreters - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a></h3>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" style="overflow-x:scroll">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-info"> 
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
                    <th width="230" align="center">Edit by</th>
                    <th width="230" align="center">&nbsp;</th>
   				</tr> 
			</thead> 
			<tbody> 
      <?php $table='hist_interpreter_reg';$view_id=$_GET['view_id'];
	  if($adLang='adLang' && $lang==''){ $query="SELECT distinct $table.* FROM $table 		
	   	where $table.id=$view_id and $table.deleted_flag=0 and  name like '$name%' and gender like '$srchgender%' and city like '$city%'	  
	  	 LIMIT {$startpoint} , {$limit}";}
		else{	  
	  $query="SELECT distinct $table.* FROM $table
		JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
		
	   	where $table.id=$view_id and $table.deleted_flag=0 and  interp_lang.lang like '$lang%' and name like '$name%' and gender like '$srchgender%' and city like '$city%'	  
	  	group by email  LIMIT {$startpoint} , {$limit}";	}
			$result = mysqli_query($con,$query);
			
			$mapCols["id"]=null;
			while($row = mysqli_fetch_array($result))
			{ 
				$dob=$row['dob']; 
				$bnakName=$row['bnakName']; 
				$acName=$row['acName'];  
				$acntCode=$row['acntCode'];  
				$acNo=$row['acNo'];?>
			            
				<tr> 
					<td id="emtpyclr"><?php if(empty($dob) || $dob== '0000-00-00' || empty($bnakName) || empty($acName) || 
						empty($acntCode) || empty($acNo)){ ?> <span style="color:#F00"><?php echo $row['name']; ?></span><?php 
						} 
						else
						{echo $row['name'];
						}?></td>

				<?php 
				  	$htm=$row['gender'];
					EchoColData($mapCols,'gender',$htm); 
					$mapCols["gender"]=$htm;
				?>
				<?php 
				  	$htm=$row['interp'];
					EchoColData($mapCols,'interp',$htm); 
					$mapCols["interp"]=$htm;
				?>
				<?php 
				  	$htm=$row['telep'];
					EchoColData($mapCols,'intteleperp',$htm); 
					$mapCols["telep"]=$htm;
				?>
				<?php 
				  	$htm=$row['trans'];
					EchoColData($mapCols,'trans',$htm); 
					$mapCols["trans"]=$htm;
				?>
				<?php 
				  	$htm=$row['city'];
					EchoColData($mapCols,'city',$htm); 
					$mapCols["city"]=$htm;
				?>
				<?php 
				  	$htm=$row['contactNo'];
					EchoColData($mapCols,'contactNo',$htm); 
					$mapCols["contactNo"]=$htm;
				?>
				<?php 
				  	$htm=$row['email'];
					EchoColData($mapCols,'email',$htm); 
					$mapCols["email"]=$htm;
				?>
				<?php 
				  	$htm=$row['sbmtd_by'];
					EchoColData($mapCols,'sbmtd_by',$htm); 
					$mapCols["sbmtd_by"]=$htm;
				?>
				<?php 
				  	$htm=$row['edited_by'].'<br/>'.$row['edited_date'];
					EchoColData($mapCols,'edited_by',$htm); 
					$mapCols["edited_by"]=$htm;
				?>

    				<td align="center"> <?php $status=$row['active']; if($status==0){echo '<span title="Click for In-active" style=" color:green;background: green;width: 15px;height: 20px;-moz-border-radius: 50px; -webkit-border-radius: 50px;border-radius: 50px;">On</span>';}else{echo '<span title="Click for Active" style=" color:red;background: red;width: 15px;height: 20px;-moz-border-radius: 50px;	-webkit-border-radius: 50px;border-radius: 50px;">On</span>';}?>
                    
                    
                    <a href="#" onClick="MM_openBrWindow('interp_data_view_edited.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&edited_date=<?php echo $row['edited_date']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				  <input type="image" src="images/icn_new_article.png" title="View Details">
			    </a>    				</tr>
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

<?php 

function EchoColData($mapref,$strCol,$htm)
{
	$ec="<td";
	if (isset($mapref[$strCol]) && $mapref[$strCol]!=$htm)
	{
		//$ec.=" style='color:#ff5536;background-color:yellow;'";
		$ec.=" style='background-color:yellow;'";
	}
	$ec.=">".$htm."</td>";
	//$mapref[$strCol]=$htm;

	//return $ec;
	echo $ec;
}

?>
