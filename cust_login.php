<?php session_start();
$role=isset($_GET['op'])?'operator':'company';
$get_op=$role=="operator"?"?op":"";
if(isset($_SESSION['cust_userId'])){
    echo "<script>window.location.href='customer_area.php';</script>";
}?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
    <title>LSUK Client Portal</title>
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
<meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
</head>
<?php if(isset($_POST['login'])){
    include'source/db.php';
    include'source/class.php';
    $UserNam=mysqli_real_escape_string($con,trim($_POST['email']));
    $Pswrd=mysqli_real_escape_string($con,$_POST['paswrd']);
    if($UserNam && $Pswrd){
        if($role=="operator"){
            $row = $acttObj->read_specific("count(*) num,company_operators.id,company_operators.name,company_login.company_id,company_login.orgName,company_operators.email,company_login.comp_type,company_operators.temp,company_operators.deleted_flag","company_operators,company_login","company_operators.company_id=company_login.id and company_operators.email='".$UserNam."' AND BINARY company_operators.paswrd='".$Pswrd."'");
        }else{
            $row = $acttObj->read_specific("count(*) num,id,company_id,orgName,email,comp_type,deleted_flag","company_login","email='".$UserNam."' AND BINARY paswrd='".$Pswrd."' AND prvlg=0");
        }
        $flag=$row['num'];
        $UserName=$row['orgName'];
        $id=$row['id'];
        if($row['deleted_flag']==1){
            $msg='<div class="alert alert-danger col-md-12 text-center"><b>Your account has been blocked. Contact LSUK !</b></div><br><br>';
        }else if($flag==0){
            $msg='<div class="alert alert-danger col-md-12 text-center"><b>Invalid Email or Password !</b></div><br><br>';
        }else{
            if($row['comp_type']==1){
                $role_name="Manager Account !<br>";
            }else if($row['comp_type']==2){
                $role_name="Staff Account !<br>";
            }else{
                $role_name=" !<br>";
            }
            $comp_nature = $acttObj->read_specific("comp_nature","comp_reg","id='".$row['company_id']."' ")['comp_nature'];
            $_SESSION['role']=$row['id']==1?'admin':'';
            $_SESSION['company_id']=$row['company_id'];
            $_SESSION['company_login_id']=$row['id'];
            $_SESSION['comp_type']=$row['comp_type'];
            $_SESSION['comp_nature']=$comp_nature;
            $_SESSION['cust_UserName']=$UserName;
            $_SESSION['cust_userId']=$id;
            if($role=="operator"){
                $_SESSION['operator']=$row['id'];
                $_SESSION['operator_name']=$row['name'];
                $_SESSION['is_temp']=$row['temp'];
            }
            $display_name=$role=="operator"?$row['name']:$row['orgName'];
            $msg='<div class="alert alert-success col-md-12 text-center h4" style="line-height:1.4;"> Greetings <b>'.$display_name.'</b>, Welcome to LSUK '.$role_name.' We are redirecting you ...</div><br><br>';?>
            <script type="text/javascript">
            setTimeout(function(){ window.location="customer_area.php"; }, 3000);</script>
    <?php }
    }
} ?>
<body style="margin-top:30px;background: #f2f2f2;">
		<div class="container-fluid">
      
		    <div class="col-md-5 col-md-offset-3" style="background: white;box-shadow: 0 0 16px 1px #d6d8d9;padding:8px;">
		    <div class="bg-info text-center" style="padding: 10px;">
		        <h4>Language Services UK Limited (LSUK)</h4>
		    </div>
		     <div class="col-md-8 col-md-offset-2"><br/>
		     <?php if(isset($msg) && !empty($msg)){echo $msg;} ?><br/>
		     </div>
			<div class="col-lg-10 col-lg-offset-1" id="div_login">
			<form id="login" method="post" action="<?php echo $_SERVER['PHP_SELF'].$get_op; ?>" enctype="multipart/form-data">
			<div class="form-group">
			    <h3>Login to client portal</h3>
		    </div>
            <div class="form-group">
                <!--Not registered as Company yet? <strong><a class="text-info" href="javascript:void(0)" onclick="alert('Action will be LIVE soon!\nKeep visiting for update');"> Sign Up</a></strong>-->
                <label class="col-md-5" for="role_company" onclick="window.location.href='cust_login.php';"><input type="radio" id="role_company" <?php if($role!="operator"){echo 'checked';}?>/> Manager</label> <label for="role_operator" onclick="window.location.href='cust_login.php?op';"><input type="radio" id="role_operator" <?php if($role=="operator"){echo 'checked';}?>/> Operator</label>
                </div>
            <div class="form-group">
		        <input type="text" name="email" id="loginEmail" value="" placeholder='Email' class="form-control" required />
		    </div>
		    <div class="form-group"> 
                <div class="input-group">
                    <input type="password" name="paswrd" id="loginPass" placeholder="Password" required  class="form-control"/>
                    <div class="input-group-btn">
                    <button id="shower" onclick="$('#loginPass').prop('type','text');$(this).hide();$('#hider').show();" class="btn btn-default" type="button">
                        <i class="glyphicon glyphicon-eye-open" title="Show Password"></i>
                    </button>
                    <button id="hider" onclick="$('#loginPass').prop('type','password');$(this).hide();$('#shower').show();" class="btn btn-default" type="button" style="display:none;">
                        <i class="glyphicon glyphicon-eye-close" title="Hide Password"></i>
                    </button>
                </div>
            </div>
				
				
			</div>
			<!-- <div class="form-group">
			<script src='https://www.google.com/recaptcha/api.js'></script>
                        <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
			</div> -->
			<div class="form-group"> 
				<input type="submit" class="btn btn-primary" name="login" value="Login to portal" />
				<input type="reset" class="btn btn-warning" value="Clear" />
			</div>
			<div class="form-group col-md-12 row">
				<br> 
                <strong><a class="text-danger" href="javascript:void(0)"  onclick="alert('Contact LSUK at Phone No : 01173290610 ');">Forgot Your Password ?</a></strong>
            </div>
			</form>
			</div>
</div>
        <p style="color:#F00;line-height:1.5;" class="col-md-5 col-md-offset-3"><b>Please note only registered companies who are issued with login ID and password can use this online portal.
        <br>If you are not registered user please click on one of the relevant forms below</b></p>
        <br/> 
    <div class="col-md-8 col-md-offset-2">
       
        <br>  
        <div class="col-sm-4">
            <a href="order_interpreter.php" class="btn btn-default">
                <center><img src="images/client_area/f2f.png" width="50" height="50" align="middle">
                    <p>Place Face to Face Interpreter Request</p>
                </center>
            </a>
        </div>
        <div class="col-sm-4">
            <a href="order_telephone.php" class="btn btn-default">
                <center><img src="images/client_area/telep.png" width="50" height="50" align="middle">
                    <p>Place Telephone Interpreter Request</p>
                </center>
            </a>
        </div>
        <div class="col-sm-4">
            <a href="order_translation.php" class="btn btn-default">
                <center><img src="images/client_area/translation.png" width="50" height="50" align="middle">
                    <p>Place Document Translation Request</p>
                </center>
            </a>
        </div>
    </div>
</div>
 <br>
        <p><b><u>Disclaimer</u></b></p>
        <p><b>Please note that only individuals who have a LSUK account and authorised access to the Online Portal should proceed beyond this point. For the security of customers, any unauthorised attempt to access LSUK information will be monitored and may be subject to legal action. Customers are reminded to keep their customer login information confidential and secure and to contact their Reseller immediately if they are aware someone else knows their login details</b></p>
<!-- end container -->
</body>
<script>
window.__lo_site_id = 300741;
	(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
		wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>
</html>