<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';$search_1=@$_GET['search_1']; $search_2=@$_GET['search_2']; $search_3=@$_GET['search_3'];$proceed=@$_GET['proceed']; if(empty($search_2)){$search_2= date("Y-m-d");}if(empty($search_3)){$search_3= date("Y-m-d");}	?>
<!doctype html>
<html lang="en">
<head>
<title>Report Multiple Invoice Paid</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.multiselect{min-width: 190px;}.multiselect-container{max-height: 400px;overflow-y: auto;max-width: 380px;}.form-group{margin-left: 16px;}</style>
</head>
<?php include "incmultiselfiles.php";?>
<script>
$(function() 
{
	$('#search_1').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true,nonSelectedText: 'Select Company' });
});
function myFunction() {
	 var x = document.getElementById("search_1").value;if(!x){x="<?php echo $search_1; ?>";}
	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	 var p = document.getElementById("proceed").value;if(!p){p="<?php echo $proceed; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?search_1=' + x + '&search_2=' + y + '&search_3=' + z + '&proceed=' + p;
	 
}
</script>
<?php include 'nav2.php';?>

<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12"><header><center><a href="<?php echo basename(__FILE__);?>">
		    <h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Multiple Invoice Paid</span></h2></a></center>
        <div class="col-md-11 col-md-offset-1"><br>
			<div class="form-group col-md-2 col-sm-4">
       <select name="proceed" id="proceed" onChange="myFunction()" class="form-control">
           <option><?php if(!empty($proceed)){ echo $proceed;}else{echo "..Proceed..";} ?></option><option>No</option><option>Cancel</option>
           </select>
          </div>
             <div class="form-group col-md-3 col-sm-4">
                 <select id="search_1" name="search_1" multiple="multiple" onChange="myFunction()" class="form-control">
                    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<option value='$code'>".$name_opt."</option>";}
?>
					<?php if(!empty($search_1)){ echo '<option selected>'.$search_1.'</option>';} ?>
                    <?php echo $options; ?>
                  </select>
          </div>
	        <div class="form-group col-md-2 col-sm-4">
        <input type="date" name="search_2" id="search_2" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_2; ?>"/>
          </div>
	        <div class="form-group col-md-2 col-sm-4">
        <input type="date" name="search_3" id="search_3" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_3; ?>" />
          </div>
        </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" align="center">
			                
			<iframe class="col-xs-10 col-xs-offset-1" height="1000px" src="pdf_script/<?php echo basename(__FILE__);?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&proceed=<?php echo $proceed; ?>" ></iframe>

		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>
</body>
</html>