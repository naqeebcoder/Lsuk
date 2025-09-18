<?php include 'db.php';include_once ('function.php');$org_name=@$_GET["org_name"];$assignCity=@$_GET["assignCity"];$dated=@$_GET["dated"];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 10;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">


<body>

	<!--<header id="header">
		<hgroup>
			<h1 class="site_title"><a href="index.html">Website Admin</a></h1>
			<h2 class="section_title">Dashboard</h2><div class="btn_view_site"><a href="http://www.medialoot.com">View Site</a></div>
		</hgroup>
	</header>--> <!-- end of header bar -->
	<?php include 'header.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header>
		  <h3 class="tabs_involved">Translation Interpreter booking list</h3>
<ul class="tabs">
            <li><a href="interp_search.php?org_name=<?php echo $org_name;?>&assignCity=<?php echo $assignCity;?>dated=<?php echo $dated;?>">Interpreter</a></li>
            <li><a href="telep_search.php?org_name=<?php echo $org_name;?>&assignCity=<?php echo $assignCity;?>dated=<?php echo $dated;?>">Telephone</a></li>
            <li><a href="trans_search.php?org_name=<?php echo $org_name;?>&assignCity=<?php echo $assignCity;?>dated=<?php echo $dated;?>">Translation</a></li>
          </ul>
        </header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Source</th>
				  <th>Target</th>
				  <th>Company Name</th>
				  <th>Booking Ref</th>
				  <th>Contact Name</th>
				  <th>Interpreter Name</th>
				  <th width="300" align="center">Actions</th>
			    </tr> 
			</thead> 
			<tbody> 
            
      <?php $table='translation';
	  $query="SELECT * FROM $table where orgName='$org_name' or invoiceNo ='$org_name' LIMIT {$startpoint} , {$limit}";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['source']; ?></td>
				  <td><?php echo $row['target']; ?></td>
				  <td><?php echo $row['orgName']; ?></td>
				  <td><?php echo $row['orgRef']; ?></td>
				  <td><?php echo $row['orgContact']; ?></td>
				  <td><?php echo $row['intrpName']; ?></td>
				  <td align="center">
                    <a href="#" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 1000, 1000);">
    				  <input type="image" src="images/icn_new_article.png" title="View Order">
    				  </a>
                    <a href="#" onClick="MM_openBrWindow('trans_edit.php?edit_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                    
                     <a href="#" onClick="MM_openBrWindow('interp_assign.php?assign_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&srcLang=<?php echo $row['source']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				    <input type="image" src="images/icn_add_user.png" title="Edit Assign Interpreter">
  				    </a>
                    
                    <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_trash.png" title="Trash"></a>
                    
                     <a href="#" onClick="MM_openBrWindow('trans_update_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/Update.png" title="Update Expanses"></a>
                     
                     <a href="#" onClick="MM_openBrWindow('trans_invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/invoice.png" title="Invoice"></a>
                    <?php if($row['invoiceNo']!=NULL && $row['invoiceNo']!='Nil'){ ?>
                     <a href="#" onClick="MM_openBrWindow('receive_amount.php?invoice_No=<?php echo $row['invoiceNo']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/Cash.png" title="Payment Received"></a>
                    <a href="#" onClick="MM_openBrWindow('co_trans_update_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/company-icon.jpg" title="Comp Update Expanses"></a>
                    <a href="#" onClick="MM_openBrWindow('comp_earning_trans.php?view_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/earning.png" title="Earning"></a>
					<!--<a href="#" onClick="MM_openBrWindow('paid_amount.php?invoice_No=<?php //echo $row['invoiceNo']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/paid.png" title="Paid to Interpreter"></a>-->
                    
                  
                    
					<?php } ?>
                    </td> 
			    </tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table, $query,$limit,$page);?></div>
		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>