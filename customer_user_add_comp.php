<!DOCTYPE HTML>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} if($_SESSION['cust_UserName']=='imran@lsuk.org'){}else{echo '<script type="text/javascript">' . "\n";echo 'window.location="index.php";'; echo '</script>';} include 'source/db.php';include 'source/class.php'; $table='comp_add_frm_web';$edit_id=@$_GET['edit_id']; $edit_id_userCode= $edit_id;  $user_code=$_SESSION['cust_userId'];?> 
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="style_form.css"/>
        <?php include'source/ajax_uniq_fun.php'; ?>
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
            <h1>Register Companies / Units for :<span style="color:#F00"> <?php echo $orgName_user=@$_GET['orgName_user'];?>'s </span>User</h1>
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
			<form class="sky-form"action="#" method="post">
							<label class="select">Company / Unit Name 
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
                    <option value="">--Select--</option>
                    <?php echo $options; ?>
                    </option>
                  </select>
                   <?php if(isset($_POST['submit'])){$c19=$_POST['orgName'];
				   
			$query="SELECT count(*) as flag FROM $table where user_code=$edit_id_userCode and orgName='$c19'";						
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){$flag=$row['flag'];}
				   if($flag==0){$edit_id= $acttObj->get_id($table);$acttObj->editFun($table,$edit_id,'orgName',$c19);}else{echo "<script>alert('Already Existed!');</script>";}} ?>
                  
							</label>
					<input type="submit" name="submit" class="button" value="Submit"/>
                            <br/>
							</form>
                        
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
<?php if(isset($_POST['submit']) && $flag==0){$acttObj->editFun($table,$edit_id,'user_code',$edit_id_userCode);echo "<script>alert('Successful!');</script>"; }?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>