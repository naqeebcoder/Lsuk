<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 
?> 

<?php 
include 'db.php';
include 'class.php';
include_once ('function.php');
$title=@$_GET['title'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];

    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>

<!doctype html>
<html lang="en">

<script>
function myFunction() {
	 var x = document.getElementById("title").value;if(!x){x="<?php echo $title; ?>";}
	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?title=' + x + '&search_2=' + y + '&search_3=' + z;
	 
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
		<header><h3 class="tabs_involved" style="width:230px;"><a href="<?php echo basename(__FILE__);?>">Registered Interpreters list</a></h3>
         <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">
        <select id="title" name="title" onChange="myFunction()" style="width:170px; height:25px;">
    <?php 			
$sql_opt="SELECT title FROM expence_list ORDER BY title ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $name_opt=$row_opt["title"];
    $options.="<OPTION value='$code'>".$name_opt.' ('.$code.')';}
?>
    <?php if(!empty($title)){ ?>
    <option><?php echo $title; ?></option>
    <?php } else{?>
    <option value="">--Select Company--</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select>
  
   |<input type="date" name="search_2" id="search_2" placeholder='' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $search_2; ?>"/> |
        <input type="date" name="search_3" id="search_3" placeholder='' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $search_3; ?>" />
        </div>

		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0" width="100%"> 
			<thead> 
				<tr>
				  <th>Title</th>
				  <th>Amount </th>
   				  <th>Details </th>
   				  <th>Voucher #</th>
   				  <th>Payment By</th>
   				  <th>Company</th> 
				  <th>Bill Date</th> 
                    <th style="color:#F00">Deleted by</th> 
    				<th width="210" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
    
	<?php 
	$table='expence';
	if($title || $search_2 || $search_3)
	{
	   $query=
	   "SELECT * FROM $table 
	   where $table.deleted_flag=1 and title like '$title%' and billDate between '$search_2' and '$search_3'
	   LIMIT {$startpoint} , {$limit}";
	}
	else
	{
	   $query="SELECT * FROM $table 
	   where $table.deleted_flag=1
	   LIMIT {$startpoint} , {$limit}";
	}

	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result))
	{
		?>            
		<tr>
			<td><?php echo $row['title']; ?></td>
			<td><?php echo $row['amoun']; ?></td> 
    		<td><?php echo $row['details']; ?></td>
    		<td><?php echo $row['voucher']; ?></td>
    		<td><?php echo $row['pay_by']?:'N/A'; ?></td>
    		<td><?php echo $row['comp']; ?></td> 
			<td><?php echo $misc->dated($row['billDate']); ?></td> 	
            <td  style="color:#F00"><?php echo $row['deleted_by'].'('.$misc->dated($row['deleted_date']).')'; ?></td>   			
    		
			<td align="center">

<?php if($_SESSION['prv']=='Management'){?>
<a href="#" onClick="MM_openBrWindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=200')"><input type="image" src="images/icn_jump_back.png" title="Restore"></a>

<a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=200')"><input type="image" src="images/icn_trash.png" title="Delete"></a> 
                    <?php } ?>
            </td> 

		</tr> 
		<?php 
	} ?>
    
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
