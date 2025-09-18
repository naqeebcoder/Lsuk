<?php if(session_id() == '' || !isset($_SESSION)){session_start();} if($_SESSION['cust_UserName']=='imran@lsuk.org'){}else{echo '<script type="text/javascript">' . "\n";echo 'window.location="index.php";'; echo '</script>';}?> 
<?php include'source/db.php'; include'source/class.php';$table='comp_login';$edit_id= @$_GET['edit_id']; 

$query="SELECT * FROM $table where id=$edit_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$orgName=$row['orgName'];$email=$row['email'];$paswrd=$row['paswrd'];} ?>

<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
<?php include'source/ajax_uniq_fun.php'; ?>
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
            <h1>Company Login Form</h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
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
		

		<div align="center" style=" color:#069; font-size:18px;">Registration</div>
			<form class="sky-form"action="#" method="post">
			  
				<fieldset><legend style="font-size:14px; color:#069;">Registration Details</legend>	
					<div class="row">
						     <section class="col col-6">
							<label class="select">Company Name 
<select id="orgName" name="orgName" required=''>
                    <?php 			
					$sql_opt="SELECT name,abrv,status FROM comp_reg
					where status <> 'Company Seized trading in' and status <> 'Company Blacklisted'
					 ORDER BY name ASC";
					$result_opt=mysqli_query($con,$sql_opt);
					$options="";
					while ($row_opt=mysqli_fetch_array($result_opt)) {
						$code=$row_opt["abrv"];
						$status=$row_opt["status"];
						$name_opt=$row_opt["name"];
						$options.="<OPTION value='$code'>".$name_opt. '<span style="color:#F00;">('.$status.')</span>';}
					?>
                    <option><?php echo $orgName; ?></option>
                    <option value="">--Select--</option>
                    <?php echo $options; ?>
                    </option>
                  </select>
           <?php if(isset($_POST['submit'])){$c1=$_POST['orgName']; $acttObj->editFun($table,$edit_id,'orgName',$c1);} ?>
							</label>
						</section>	
						
						<section class="col col-6">
							<label class="input">Email 
      <input name="email" type="text" id="unique" onBlur="uniqueFun();" value="<?php echo $email; ?>" placeholder='' required='' />
      <?php if(isset($_POST['submit'])){$c1=$_POST['email']; $acttObj->editFun($table,$edit_id,'email',$c1);} ?>
							</label>
						</section>
						</div>
                        <div class="row">
						<section class="col col-6">
							<label class="input">Password 
      <input name="paswrd" type="password" id="paswrd"  onchange="form.repass.pattern = this.value;" value="<?php echo $paswrd; ?>" placeholder='' required='' pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"/>
      <?php if(isset($_POST['submit'])){$c2=$_POST['paswrd']; $acttObj->editFun($table,$edit_id,'paswrd',$c2);} ?>
							</label>
						</section>
                        <section class="col col-6">
							<label class="input">Re-Password 
      <input name="repass" type="password" id="repass" value="<?php echo $paswrd; ?>" placeholder='' required='' pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" />
							</label>
						</section>
					</div>

				</fieldset>	
				<footer>
					<input type="submit" name="submit" class="button" value="Submit"/>
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
<?php if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";}?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>