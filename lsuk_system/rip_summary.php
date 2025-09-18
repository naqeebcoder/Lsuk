<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php'; $search_2=@$_GET['search_2']; $search_3=@$_GET['search_3']; if(empty($search_2)){$search_2= date("Y-m-d");}if(empty($search_3)){$search_3= date("Y-m-d");}	?>
<!doctype html>
<html lang="en">
<head>
<title>Profit and Loss Summary</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<script>
function myFunction() {
	
	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?search_2=' + y + '&search_3=' + z;
	 
}
</script>
<?php include 'nav2.php';?>

<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header><center><a href="<?php echo basename(__FILE__);?>">
		    <h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Profit and Loss Summary</span></h2></a></center>
			<div class="col-md-11 col-md-offset-1"><br>
			<div class="form-group col-md-2 col-sm-4">
        <input type="date" name="search_2" id="search_2" class="form-control" value="<?php echo $search_2; ?>"/>
          </div>
             <div class="form-group col-md-2 col-sm-4">
        <input type="date" name="search_3" id="search_3"  class="form-control" value="<?php echo $search_3; ?>" />
          </div>
	        <div class="form-group col-md-1 col-sm-4">
         <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction()"><span class="btn btn-sm btn-primary">Get Report</span></a>
          </div>
             <div class="form-group col-md-2 col-sm-4">
                <a href="reports_lsuk/excel/<?php echo basename(__FILE__);?>?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
          </div>
        
        </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" align="center">
			                
			<iframe id="myFrame" class="col-xs-10 col-xs-offset-1" style="height:180rem;" src="reports_lsuk/pdf/<?php echo basename(__FILE__);?>?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" ></iframe>

		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    function MM_openBrWindow(theURL,winName,features) {
  window.open(theURL,winName,features);}
</script>
</html>