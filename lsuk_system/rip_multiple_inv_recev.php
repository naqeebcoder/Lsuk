<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 
?> 

<?php 
include 'db.php';
include 'class.php';
$orgs=array();
$multInvoicNo=@$_GET['multInvoicNo'];
$search_1=@$_GET['search_1']; 
$search_2=@$_GET['search_2']; 
$search_3=@$_GET['search_3'];
$proceed=@$_GET['proceed']; 
$p_org=@$_GET['p_org']; 
if(empty($search_2))
{
	$search_2= date("Y-m-d");
}
if(empty($search_3))
{
	$search_3= date("Y-m-d");
}	
if (isset($search_1) && $search_1 != "") {
    $orgs = explode(",", $search_1);
}
?>
<!doctype html>
<html lang="en">
<head>
<title>Report Multiple Invoice Received</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.multiselect{min-width: 190px;}.multiselect-container{max-height: 400px;overflow-y: auto;max-width: 380px;}.form-group{margin-left: 16px;}</style>
</head>
<?php include "incmultiselfiles.php";?>
<script>
$(function() 
{
	$('#search_1').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true,nonSelectedText: 'Select Company' });
});
// function myFunction() { var a = "<?php echo $multInvoicNo; ?>";
// 	 var x = document.getElementById("search_1").value;if(!x){x="<?php echo $search_1; ?>";}
// 	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
// 	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
// 	 var p = document.getElementById("proceed").value;if(!p){p="<?php echo $proceed; ?>";}
// 	 window.location.href="<?php echo basename(__FILE__);?>" + '?multInvoicNo=' + a + '&search_1=' + x + '&search_2=' + y + '&search_3=' + z + '&proceed=' + p;
	 
// }
function myFunction() {
	var a = "<?php echo $multInvoicNo; ?>";
	var x = $("#search_1").val();if(!x){x="<?php echo $search_1; ?>";}
	var y = $("#search_2").val();if(!y){y="<?php echo $search_2; ?>";}
	var z = $("#search_3").val();if(!z){z="<?php echo $search_3; ?>";}
	var p = $("#proceed").val();if(!p){p="<?php echo $proceed; ?>";}
	var p_org = $("#p_org").val();if(!p_org){p_org="<?php echo $p_org; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?multInvoicNo=' + a +'&p_org='+p_org+'&search_1=' + x + '&search_2=' + y + '&search_3=' + z + '&proceed=' + p;
	 
}
</script>
<?php include 'nav2.php';?>

<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12"><header><center><a href="<?php echo basename(__FILE__);?>">
		    <h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Multiple Invoice's Status Update</span></h2></a></center>
        <div class="col-md-11 col-md-offset-1"><br>
			<div class="form-group col-md-2 col-sm-4">
       <select name="proceed" id="proceed" onChange="myFunction()" class="form-control">
           <option><?php if(!empty($proceed)){ echo $proceed;}else{echo "..Proceed..";} ?></option><option>No</option><option value="Received">Payment Received</option><option value="Undo">Payment Undo</option><option>Cancel</option>
           </select>
          </div>
		  <div class="form-group col-md-2 col-sm-4 p_org_div">
				<?php
				$result_opt = $acttObj->read_all("DISTINCT id,name,abrv","comp_reg"," comp_nature=1 AND deleted_flag=0 ORDER BY name ASC "); ?>
				<select id="p_org" name="p_org" class="form-control searchable">
					<?php 
					$options = "";
					while ($row_opt = mysqli_fetch_array($result_opt)) {
						$comp_id = $row_opt["id"];
						$code = $row_opt["abrv"];
						$name_opt = $row_opt["name"];
						$options .= "<OPTION value='$comp_id' " . ($comp_id == $p_org ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
					}
					?>
					<option value="">Select Parent/Head Units</option>
					<?php echo $options; ?>
					</option>
				</select>
			</div>
			<div class="form-group col-md-2 col-sm-4 search_1_div">
                 <select id="search_1" name="search_1" multiple class="form-control">
                    <?php 			
					$result_opt= $acttObj->read_all("id,name,abrv","comp_reg"," 1 ORDER BY name ASC ");
					$options="";
					while ($row_opt=mysqli_fetch_array($result_opt)) {
						$code=$row_opt["id"];
						$name_opt=$row_opt["name"];
						$options.="<option value='$code' ".(in_array($code,$orgs)?'selected':'').">".$name_opt."</option>";						
					}
					?>
                    <?php echo $options; ?>
                  </select>
          	</div>
	        <div class="form-group col-md-2 col-sm-4">
        <input type="date" name="search_2" id="search_2" placeholder='' class="form-control" value="<?php echo $search_2; ?>"/>
          </div>
	        <div class="form-group col-md-2 col-sm-4">
        <input type="date" name="search_3" id="search_3" placeholder='' class="form-control" value="<?php echo $search_3; ?>" />
          </div>
	        <div class="form-group col-md-1 col-sm-4">
                <a href="reports_lsuk/excel/<?php echo basename(__FILE__);?>?p_org=<?php echo $p_org; ?>&search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&multInvoicNo=<?php echo $multInvoicNo; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
        </div>
        </div>
		</header>
		<div class="tab_container">
			<div id="tab1" class="tab_content" align="center">
			                
			<iframe class="col-xs-10 col-xs-offset-1" height="1000px" src="reports_lsuk/pdf/<?php echo basename(__FILE__);?>?p_org=<?php echo $p_org; ?>&multInvoicNo=<?php echo $multInvoicNo; ?>&search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&proceed=<?php echo $proceed; ?>" ></iframe>

		  </div>
		</div>
		</article>
    <div class="clear"></div>		
		<div class="spacer"></div>
	</section>
</body>
</html>
<script>
$(document).on('change','#p_org',function(){
	$('.search_1_div').hide();
});
$(document).on('change','#search_1',function(){
	$('.p_org_div').hide();
});
</script>