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
            	<h2>No of Applicants for Post of <?php echo "Staff";?></h2>
                <table class="gen-table">
                    <!--<caption>
                    Table Caption
                    </caption>-->
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th width="70">Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                      <tbody>
 <?php $table='staff_job_applicants'; $query="SELECT * FROM $table
 
   LIMIT {$startpoint} , {$limit}";	

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>                       
                        <tr>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['contact']; ?></td>
                            <th><?php echo $row['email']; ?></th>
                            <td><?php echo $row['location']; ?></td>
                            <td><?php echo $misc->dated($row['dated']); ?></td>
                            <td> <?php if(@$_SESSION['email']=="imran@lsuk.org" || $_SESSION['interp_code']=='id-13'){ ?>                       
                    <a class="button blue-2" href="job_reply.php?rep_id=<?php echo $row['id']; ?>&email=<?php  echo $row['email']; ?>&name=<?php  echo $row['name']; ?>">Reply</a>
                    
                     <?php } ?></td>
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