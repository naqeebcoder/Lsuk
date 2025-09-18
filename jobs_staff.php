<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'source/db.php';include 'source/class.php';include_once ('source/function.php');
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 20;
    	$startpoint = ($page * $limit) - $limit;	?>
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href="new_theme/css/bootstrap.min.css" rel="stylesheet">
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <script src="source/jquery-2.1.3.min.js"></script> 
  <script type="text/javascript" src="new_theme/js/bootstrap.min.js"></script>
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
            <h1>Interpreting Jobs</h1>
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
            <section style="overflow-x:auto;">
            	<h3>LSUK is looking to hire the services of  <?php if(@$_GET['val']=='interpreter'){echo 'Face to Face'; } if(@$_GET['val']=='telephone'){echo 'Voice Over'; }?></h3>
                <table class="gen-table">
                    <!--<caption>
                    Table Caption
                    </caption>-->
                    <thead>
                        <tr>
                            <th>Job title</th>
                            <th>Job Type</th>
                            <th>Essential Skills</th>
                            <th>Job Description</th>
                            <th>Job Location</th>
                            <th>Last Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                      <tbody>
 <?php $table='staff_job'; $query="SELECT * FROM $table LIMIT {$startpoint} , {$limit}";	

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>                       
                        <tr>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['type']; ?></td>
                            <td><?php echo $row['skill']; ?></td>
                            <th><?php echo $row['descrp']; ?></th>
                            <th><?php echo $row['location']; ?></th>
                            <th><?php echo $misc->dated($row['last_date']); ?></th>
                            <td><a class="button blue-2" href="job_apply_staff.php?tracking=<?php echo $row['id']; ?>&table=<?php  echo $table; ?>&title=<?php  echo $row['title']; ?>">Apply</a>
                            
                    <?php if(@$_SESSION['email']=="imran@lsuk.org" || $_SESSION['interp_code']=='id-13'){ ?>         
                    <a class="button blue-2" href="no_of_applicants_staff.php?tracking=<?php echo $row['id']; ?>&table=<?php  echo $table; ?>">Applicants</a>
                    <a class="button blue-2" href="del.php?del_id=<?php echo $row['id']; ?>&table=<?php  echo $table; ?>">Delete</a>
                    
                 
                    
                     <?php } ?>
                            </td>
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