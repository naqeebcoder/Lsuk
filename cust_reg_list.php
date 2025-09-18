<?php if(session_id() == '' || !isset($_SESSION)){session_start();} if($_SESSION['cust_UserName']=='imran@lsuk.org'){}else{echo '<script type="text/javascript">' . "\n";echo 'window.location="index.php";'; echo '</script>';}?> 
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
            <h1>Users List</h1>
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
            	<h2>No of Registered Users</h2>
                <table class="gen-table">
                    <!--<caption>
                    Table Caption
                    </caption>-->
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Dated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                      <tbody>
 <?php $table='comp_login'; $query="SELECT * FROM $table LIMIT {$startpoint} , {$limit}";	

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>                       
                        <tr>
                            <td><?php echo $row['orgName']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['paswrd']; ?></td>
                            <th><?php echo $row['dated']; ?></th>
                            <td>
                            <a class="button blue-2" href="cust_reg_edit.php?edit_id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="button blue-2" href="customer_user_add_comp.php?edit_id=<?php echo $row['id']; ?>&orgName_user=<?php echo $row['orgName']; ?>">Add Companies</a>
                            <a class="button blue-2" href="customer_user_add_comp_list.php?edit_id=<?php echo $row['id']; ?>&orgName_user=<?php echo $row['orgName']; ?>">Added Comp's list</a>
                            <?php if($row['prvlg']==0){ ?>
                            <a class="button blue-2" href="cust_user_block.php?edit_id=<?php echo $row['id']; ?>&prvlg=1&table=<?php echo $table; ?>">Block</a>
                            <?php }else{  ?>
                            <a class="button blue-2" href="cust_user_block.php?edit_id=<?php echo $row['id']; ?>&prvlg=0&table=<?php echo $table; ?>">Un-Block</a>
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