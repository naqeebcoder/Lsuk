<?php if(session_id() == '' || !isset($_SESSION)){session_start();} if($_SESSION['cust_UserName']=='imran@lsuk.org'){}else{echo '<script type="text/javascript">' . "\n";echo 'window.location="index.php";'; echo '</script>';}?> 
<?php include 'source/db.php';include 'source/class.php';include_once ('source/function.php');$tracking=@$_GET['tracking'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
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
            <h1>Archive</h1>
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
            	<h2>Translation Orders List</h2>
                <table class="gen-table">
                    <!--<caption>
                    Table Caption
                    </caption>-->
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Source</th>
                            <th>Assignment Date</th>
                            <th>Document Type</th>
                        </tr>
                    </thead>
                    
                      <tbody>
 <?php $table='translation'; $cust_userId= $_SESSION['cust_userId']; 
 		$query="SELECT $table.orgName,$table.source,$table.asignDate,$table.docType FROM $table
	   	JOIN  comp_add_frm_web ON $table.orgName=comp_add_frm_web.orgName
	   	where  $table.deleted_flag=0 and $table.order_cancel_flag=0 and comp_add_frm_web.user_code=$cust_userId order by asignDate desc LIMIT {$startpoint} , {$limit}";

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>                       
                        <tr>
                            <td><?php echo $row['orgName']; ?></td>
                            <td><?php echo $row['source']; ?></td>
                            <td><?php echo $misc->dated($row['asignDate']); ?></td>
                            <th><?php echo $row['docType']; ?></th>
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