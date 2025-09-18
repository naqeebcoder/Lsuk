<?php session_start();
error_reporting(0);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
if(isset($_SESSION['web_userId'])){
    echo "<script>window.location.href='interp_profile.php';</script>";
}
/*else{
    if(!empty($_COOKIE['device_id'])){
        setcookie("device_id", "", time() - 3600);
    }
}*/
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="js/jquery-1.8.2.min.js"></script>
    <title>LSUK Interpreter Portal</title>
    <style>
        body{position: absolute;top: 0;bottom: 0;left: 0;right: 0;display: flex;justify-content: space-around;align-items: center;flex-wrap: wrap; background-color:#FAFAFA;}
    </style>
    <style>input[type=submit] {
        background: none;
        height: 35px;
        padding: 0 15px;}
        input[type=submit] {
            color: #000;font-weight:bold;}
            input[type=submit]:hover {
                color: #ffffff;
            }</style>
            <?php if(isset($_POST['btn_forgot'])){
                include'source/db.php';
                include'source/class.php';
                $forgotEmail=mysqli_real_escape_string($con,$_POST['forgotEmail']); 
                if($forgotEmail){
                    $query="SELECT count(*) num,interpreter_reg.id, interpreter_reg.is_temp, interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.password, interpreter_reg.email,interpreter_reg.deleted_flag,interpreter_reg.active FROM interpreter_reg where email='$forgotEmail'";          
                    $result = mysqli_query($con,$query);
                    $row = mysqli_fetch_assoc($result);
                    $flag=$row['num'];$UserName=$row['name'];
                    $id=$row['id'];$email=trim($row['email']);
                    $contactNo=$row['contactNo'];
                    $password=trim($row['password']);
                    $is_temp=$row['is_temp'];
                    $deleted_flag=$row['deleted_flag'];
                }
                if($is_temp==1){
                    $get_msg_db=$obj->read_specific('message','auto_replies','id=7');
                    $json->msg=$get_msg_db['message'];
                }else if($flag==0 || is_null($deleted_flag)){
                    $msg='<div class="alert alert-danger col-md-10 text-center"><b>Entered Email Not Found at LSUK!</b></div><br><br>';
                }else if($deleted_flag=='1' || $row['active'] == 1){
                    $get_msg_db=$acttObj->read_specific('message','auto_replies','id=2');
                    $msg='<div class="alert alert-danger col-md-12 text-center"><b>'.$get_msg_db['message'].'</b></div><br><br>';
                }else{
                    if($flag==1){
                        if(empty($password) || is_null($password)){
                            $new_password='@'.strtok($row['name'], " ").substr(str_shuffle('0123456789abcdwxyz') , 0 , 3 ).substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0 , 3 );
                            $acttObj->editFun('interpreter_reg',$id,'password',$new_password);
                            $password=$acttObj->read_specific('password','interpreter_reg','id='.$id)['password'];
                        }
                        try {
                            $to_add=$forgotEmail;
                            $from_add = "hr@lsuk.org";
                            $em_format=$acttObj->read_specific("em_format","email_format","id=35")['em_format'];
                            $data   = ["[PASSWORD]"];
                            $to_replace  = [$password];
                            $message=str_replace($data, $to_replace,$em_format);
                            $mail->SMTPDebug = 0;
                        //$mail->isSMTP(); 
                        //$mailer->Host = 'smtp.office365.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'info@lsuk.org';
                            $mail->Password   = 'LangServ786';
                            $mail->SMTPSecure = 'tls';
                            $mail->Port       = 587;
                            $mail->setFrom($from_add, 'LSUK Account Security');
                            $mail->addAddress($to_add);
                            $mail->addReplyTo($from_add, 'LSUK');
                            $mail->isHTML(true);
                            $mail->Subject = 'Password recovery for LSUK online portal';
                            $mail->Body = $message;
                            $mail->send();
                            $mail->ClearAllRecipients();
                            $msg='<div class="alert alert-success col-md-12 text-center">Thanks <b>'.ucwords($UserName).' !</b> We have sent password to your email.<br>Kindly check and try login again.<br>If still you have problem with login than Contact LSUK at Ph: 01173290610</div><br><br>';
                        } catch (Exception $e) {
                            $msg='<div class="alert alert-danger col-md-10 text-center">There was problem sending email.Try again.</div><br><br>';
                        }
                    }
                }
            }
            if(isset($_POST['login'])){
                include'source/db.php';
                        include'source/class.php';
                        $UserNam=mysqli_real_escape_string($con,trim($_POST['loginEmail']));
                        $Pswrd=mysqli_real_escape_string($con,$_POST['loginPass']);
                        if($UserNam && $Pswrd){
                            $query="SELECT count(*) num,interpreter_reg.id, interpreter_reg.is_temp, interpreter_reg.name,interpreter_reg.contactNo, interpreter_reg.email,interpreter_reg.code,interpreter_reg.gender,interpreter_reg.address,interpreter_reg.deleted_flag,interpreter_reg.active FROM interpreter_reg where  TRIM(email)='".$UserNam."' AND BINARY TRIM(password)='".$Pswrd."'";         
                            $result = mysqli_query($con,$query);
                            $row = mysqli_fetch_assoc($result);
                            $flag=$row['num'];$UserName=$row['name'];
                            $id=$row['id'];$email=trim($row['email']);
                            $contactNo=$row['contactNo'];
                            $gender=$row['gender'];
                            $interp_code=$row['code'];
                            $is_temp=$row['is_temp'];
                            $deleted_flag=$row['deleted_flag'];
                        }
                        if($is_temp==1){
                            $get_msg_db=$acttObj->read_specific('message','auto_replies','id=7');
                            // $json->msg=$get_msg_db['message'];
                            $msg='<div class="alert alert-danger col-md-10 text-center"><b>'.$get_msg_db['message'].'</b></div><br><br>';
                        }else if($flag==0 || is_null($deleted_flag)){
                            $get_msg_db=$acttObj->read_specific('message','auto_replies','id=1');
                            $msg='<div class="alert alert-danger col-md-10 text-center"><b>'.$get_msg_db['message'].'</b></div><br><br>';
                        }else if($deleted_flag=='1' || $row['active'] == 1){
                            $get_msg_db=$acttObj->read_specific('message','auto_replies','id=2');
                            $msg='<div class="alert alert-danger col-md-12 text-center"><b>'.$get_msg_db['message'].'</b></div><br><br>';
                        }else{
                            if($flag==1){
                //$_SESSION['UserName']=$UserName;
                                $_SESSION['web_UserName']=$UserName;
                                $_SESSION['web_userId']=$id;    
                                $_SESSION['email']=$email;
                                $_SESSION['gender']=$gender;            
                                $_SESSION['interp_code']=$interp_code;
                /*if(!empty($_POST['token'])){
                    $_SESSION['device_id']=$acttObj->read_specific("uuid() as device_id","int_tokens","1")["device_id"];
                    $_SESSION['token']=$_POST['token'];
                    $acttObj->insert("int_tokens",array("device_id"=>$_SESSION['device_id'],"int_id"=>$_SESSION['web_userId'],"token"=>$_POST['token'],"dated"=>date("Y-m-d h:i:s")));
                }*/
                $msg='<div class="alert alert-success col-md-12 text-center">Greetings <b>'.ucwords($UserName).'.</b> Welcome to LSUK!</div><br><br>';
                echo '<script type="text/javascript">' . "\n";  
                echo 'setTimeout(function(){ window.location="interp_profile.php"; }, 3000);'; echo '</script>';
            }
        }
} ?>
</head>
<body style="background: #f2f2f2;">
    <div class="container">
        <div style="background: white;box-shadow: 0 0 16px 1px #d6d8d9;padding:8px;display:none;">
            <span class="h5"><strong>IMPORTANT NOTE</strong>
                <br>Please use your temporary password that you just received in your email from LSUK. 
                <br>Your new password should be atleast 8 characters long and should include a mixture of CAPITAL letters Numbers and small letters and or symbols.  
            eg. Lsuk1234</span>
        </div>
        <div class="col-md-8 col-md-offset-2" style="background: white;box-shadow: 0 0 16px 1px #d6d8d9;">
            <div class="col-md-12">
               <h3 class="text-center">Language Services UK Limited (LSUK)</h3><hr/>
           </div>
           <div class="col-md-8 col-md-offset-2">
               <?php if(isset($msg) && !empty($msg)){echo $msg;} ?><br/>
           </div>
           <div class="form-group col-md-6 col-md-offset-5">
            <br> 
            Not registered as Interpreter yet? <strong><a class="text-info" href="interp_reg.php"> Sign Up</a></strong>
        </div>
        <div class="col-md-8 col-md-offset-2" id="div_login">
           <form id="login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                <div class="form-group"> 
                    <input type="text" name="loginEmail" id="loginEmail" value="" placeholder='Email' class="form-control" required />
                </div>
                <div class="form-group"> 
                    <div class="input-group">
                        <input type="password" name="loginPass" id="loginPass" placeholder="Password" required  class="form-control"/>
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
                <br>
                <!-- <div class="form-group">
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                    <div class="g-recaptcha" data-sitekey="6LextRoUAAAAAGSGzslurL5xeNDw3lDDVkxM9rZe"></div>
                </div> -->
                <div class="form-group"> 
                    <input type="submit" class="btn btn-primary col-md-4" name="login" value="Log in" />
                    <input type="reset" class="btn btn-warning col-md-4 col-md-offset-1" value="Clear" />
                </div>
                <div class="form-group col-md-12 row">
                    <br> 
                    <strong><a class="text-danger" href="javascript:void(0)" onclick="document.getElementById('div_forgot').style.display='inline-block';document.getElementById('div_login').style.display='none';" rel="submenu">Forgot Your Password ?</a></strong>
                </div>
            </form>
        </div>
        <div class="col-md-8 col-md-offset-2" id="div_forgot" style="display:none;">
           <form id="frm_forgot" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <div class="form-group"> 
                <input type="text" name="forgotEmail" id="forgotEmail" value="" placeholder='Enter your Email to get your password' class="form-control" required />
            </div>
            <div class="form-group"> 
                <input type="submit" class="btn btn-primary col-md-4" name="btn_forgot" value="Send Now" />
                <input type="reset" class="btn btn-warning col-md-4 col-md-offset-1" value="Clear" />
            </div>
            <div class="form-group col-md-12 row">
                <br> 
                <strong><a class="text-danger" href="javascript:void(0)" onclick="document.getElementById('div_login').style.display='inline-block';document.getElementById('div_forgot').style.display='none';" rel="submenu">← Back to Login</a></strong>
            </div>
        </form>
    </div>
    <div class="col-md-8 col-md-offset-2">
        <hr/>
        <p class="text-center">Created by <a href="https://www.softechbusinessservices.com/" class="text-info"><b>Softech Business Services</b></a></p><br/>
    </div>
</div>
</div>
</body>
    <!--<script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js"></script>-->
        <script>
    /*var config = {
            apiKey: "AIzaSyCUsl-EHHeA4HvBDRyOXdyCQSMNmUiRlPc",
            authDomain: "lsuk-1530684014975.firebaseapp.com",
            projectId: "lsuk-1530684014975",
            storageBucket: "lsuk-1530684014975.appspot.com",
            messagingSenderId: "62740450561",
            appId: "1:62740450561:web:40eadc0959b6be3a881f00"
        };
        firebase.initializeApp(config);
        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function () {
                //console.log("Notification permission granted.");
                // get the token in the form of promise
                return messaging.getToken()
            })
            .then(function(token) {
                $("#token").val(token);
            })
            .catch(function (err) {
                console.log("Unable to get permission to notify.", err);
            });*/
    window.__lo_site_id = 300741;
	(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
		wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>
        </html>