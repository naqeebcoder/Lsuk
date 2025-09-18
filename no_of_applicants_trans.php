<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'source/db.php';include 'source/class.php';include_once ('source/function.php');$tracking=@$_GET['tracking'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="boxed">
<!-- begin container -->
<div id="wrap">
	<!-- begin header -->
<?php include'source/top_nav.php'; ?>
    <!-- end header -->
	
    <!-- begin page title -->
    <section id="page-title">
    	<div class="container clearfix">
            <h1>Applicants List</h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                </ul>
            </nav>
        </div>
    </section>
    <!-- begin page title -->
    
    <!-- begin content -->
    <section id="content" class="container clearfix">
        	            <!-- begin table -->
            <section>
            	<h2>No of Applicants for  <?php if(@$_GET['table']=='interpreter'){echo 'Face to Face'; } if(@$_GET['table']=='telephone'){echo 'Voice Over'; }?></h2>
                <table class="gen-table">
                    <!--<caption>
                    Table Caption
                    </caption>-->
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Target</th>
                            <th>Date</th>
                            <th>No of Units</th>
                            <th>Bid Amount &pound;</th>
                        </tr>
                    </thead>
                    
                      <tbody>
 <?php $table=@$_GET['table']; $query="SELECT $table.*, bid.* FROM $table
 JOIN bid ON $table.id=bid.job
 
  where bid.job=$tracking and intrpName= '' LIMIT {$startpoint} , {$limit}";	

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>                       
                        <tr>
                            <td><?php echo $row['source']; ?></td>
                            <td><?php echo $row['target']; ?></td>
                            <td><?php echo $misc->dated($row['asignDate']); ?></td>
                            <td><?php echo $row['numberUnit']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8"><?php echo pagination($con,$table,$query,$limit,$page);?></td>
                        </tr>
                    </tfoot>
                  
                </table>
            </section>
            <!-- end table -->	
        
       
   <hr>
        
     	<!-- begin clients -->
       <?php include'source/our_client.php'; ?>
        <!-- end clients -->   
    </section>
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<!-- end container -->
</body>

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:49:13 GMT -->
</html>