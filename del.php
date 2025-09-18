<?php
error_reporting(E_ALL);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://countriesnow.space/api/v0.1/countries/states',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_COOKIEJAR => dirname(__FILE__) . '/cookie.txt',
  CURLOPT_COOKIEFILE => dirname(__FILE__) . '/cookie.txt',
  CURLOPT_POSTFIELDS =>'{
    "country": "United Kingdom"
}',
));

$response = curl_exec($curl);
echo curl_error($curl);
curl_close($curl);
echo 'data = '.$response;exit;


?>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 

<?php include'source/db.php'; include'source/class.php'; if(isset($_POST['submit'])){$table='staff_job_applicants';$edit_id= $acttObj->get_id($table);}?>

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

            <h1>Deletion of Record </h1>

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

    	<!-- begin our company -->

 

    <!-- begin content -->

    <section id="content" class="container clearfix">

  <link rel="stylesheet" type="text/css" href="style_form.css"/>

		



		<div align="center" style=" color:#069; font-size:18px;"> Are you sure you want to delete this record <span style="color:#F00; font-weight:bold">Permanantly</span> <?php echo @$_GET['title']; ?></div>

			<form class="sky-form" action="#" method="post">

			 

              				 

				

				<footer>

		<input type="submit" name="yes" value="Yes"  class="button" />&nbsp;&nbsp;<input type="submit" name="no" value="No" onclick="goBack()"  class="button" />

				</footer>

</form>



    </section>

    <!-- end content -->  

    

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

<?php if(isset($_POST['yes'])){

 $del_id = @$_GET['del_id'];$table = @$_GET['table'];$url = @$_GET['url'];

 include 'source/db.php';mysqli_query($con,"DELETE FROM $table WHERE id=$del_id");mysqli_close($con); 

 

 if($table=='blog'){echo "<script>window.location.assign('blog.php')</script>";}if($table=='comp_add_frm_web'){echo "<script>window.location.assign('customer_user_add_comp_list.php')</script>";}else{echo "<script>window.location.assign('jobs_staff.php')</script>";}} ?>

 <script>function goBack() {window.history.back();}</script>

