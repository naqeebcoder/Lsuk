<?php 
if(session_id() == '' || !isset($_SESSION)){session_start();}
error_reporting(0);
//include 'secure.php';
include 'source/db.php';
include 'source/class.php';
include_once ('source/function.php');
?>
<head>
  <?php include'source/header.php'; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="boxed">
<div id="wrap">
<?php include'source/top_nav.php'; ?>
 <?php if(isset($_POST['login'])){
$Pswrd=$_POST['loginPass']; 
$UserNam=$_POST['loginEmail'];
if($UserNam && $Pswrd){
    $query="SELECT count(*) num,interpreter_reg.id, interpreter_reg.is_temp, interpreter_reg.name,interpreter_reg.contactNo, interpreter_reg.email,interpreter_reg.code,interpreter_reg.gender,interpreter_reg.address,interpreter_reg.deleted_flag,
    (CASE WHEN ((interpreter_reg.active='0' AND interpreter_reg.actnow='Active') OR (interpreter_reg.active='0' AND interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) THEN 'ready' ELSE 'not ready' END) as status FROM interpreter_reg where  TRIM(email)='".$UserNam."' AND BINARY TRIM(password)='".$Pswrd."'";			
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_array($result);
    $flag=$row['num'];$UserName=$row['name'];
    $id=$row['id'];$email=trim($row['email']);
    $contactNo=$row['contactNo'];
    $gender=$row['gender'];
    $interp_code=$row['code'];
    $status=$row['status'];
    $is_temp=$row['is_temp'];
    $deleted_flag=$row['deleted_flag'];
    }
    if($is_temp==1){
        $get_msg_db=$obj->read_specific('message','auto_replies','id=7');
        $json->msg=$get_msg_db['message'];
    }else if($flag==0 || is_null($deleted_flag)){
        $get_msg_db=$acttObj->read_specific('message','auto_replies','id=1');
        $msg='<div class="alert alert-danger col-md-10 text-center"><b>'.$get_msg_db['message'].'</b></div><br><br>';
    }else if($status=='not ready' || $deleted_flag=='1'){
        $get_msg_db=$acttObj->read_specific('message','auto_replies','id=2');
        $msg='<div class="alert alert-danger col-md-12 text-center"><b>'.$get_msg_db['message'].'</b></div><br><br>';
    }else{
        if($flag==1){
            $_SESSION['web_UserName']=$UserName;
            $_SESSION['web_userId']=$id;	
            $_SESSION['email']=$email;
            $_SESSION['web_contactNo']=$contactNo;
            $_SESSION['web_address']=$address;
            $_SESSION['gender']=$gender;			
            $_SESSION['interp_code']=$interp_code;	
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $result_url="https://lsuk.org/update_password.php";
            if($url!=$result_url){
                echo "<script>setTimeout(function(){ window.location.href='$result_url'; }, 3500);</script>"; 
            }else{
                echo "<script>window.location.href='$result_url';</script>";
            }
        }
    }
}
if(isset($_SESSION['web_userId']) & isset($_POST['interpreter_submit']) & !empty($_POST['upassword']) & !empty($_POST['cpassword'])){
    $interp_id=$_SESSION['web_userId'];
     $upassword=mysqli_real_escape_string($con,$_POST['upassword']);
     $cpassword=mysqli_real_escape_string($con,$_POST['cpassword']);
     if($upassword!=$cpassword){
         $msg="<p class='msg'>Information : Entered password and confirmed password must be same!</p>";
     }else{
         $acttObj->editFun('interpreter_reg',$interp_id,'password',$upassword);
    $msg="<p class='alert alert-success'>Thank you! You password has been successfully updated.</p>";
    echo "<script>setTimeout(function(){ window.location.href='$result_url'; }, 3500);</script>";
     }
 }
?>

    <!-- begin page title -->
    <section id="page-title">
    	<div class="container clearfix">
            <h2>Update Password</h2>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                </ul>
            </nav>
        </div>
    </section>
    <!-- begin page title -->
    
    <!-- begin content -->
    <section id="content" class=" clearfix">
    <section>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		  <style>.text-red{color: red;}</style>  
    <?php if(isset($_SESSION['web_userId'])){ ?>
<div class="col-md-6 col-md-offset-3">
		     <?php if(isset($msg) && !empty($msg)){echo $msg;} ?><br/>
		     </div>
			<div class="col-md-6 col-md-offset-3">
			 <form id="upd_pass" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" autocomplete="off">
			     <div class="form-group"> 
			     <h3>Update your password here</h3>
			     </div>
			   <div class="form-group"> 
		    <input type="password" oninput="p();p3();" onkeyup="p1()" onkeydown="p2()" onkeypress="p3()" id="upassword" name="upassword" class="form-control" required placeholder="New password" autocomplete="off"/>
		    </div>
			   <div class="form-group" id="msg_note" style="display:none">
			       <!--<center><b><i class="fa fa-angle-up fa-2x"></i></b></center>-->
			       <div class="panel panel-info">
                      <div class="panel-heading">Password must meet the following requirements</div>
                      <div class="panel-body">
                          <ul class="list-group" id="list-group">
                            <li id="letter" class="text-red"><i class="fa fa-times-circle"></i> At least <strong>one letter</strong></li>
                            <li id="capital" class="text-red"><i class="fa fa-times-circle"></i> At least <strong>one capital letter</strong></li>
                            <li id="number" class="text-red"><i class="fa fa-times-circle"></i> At least <strong>one number</strong></li>
                            <li id="length" class="text-red"><i class="fa fa-times-circle"></i> Be at least <strong>8 characters</strong></li>
                        </ul>
                </div>
		    </div>
		    </div>
		    <div class="form-group"> 
		    <input type="password" oninput="confirmpass();" onfocus="p();" id="cpassword" name="cpassword" class="form-control" required placeholder="Confirm password" autocomplete="off"/>
		    <ul class="list-group" id="error_confirm" style="display:none">
                            <li id="letter" class="text-red"><i class="fa fa-times-circle"></i> <strong>Password and Confirm Password must be same !</strong></li>
                        </ul>
		    </div>
		    <div class="form-group"> 
				<input type="checkbox" onclick="showpassword()"> Show Password
			</div>
			<div class="form-group"> 
				<input type="submit" name="interpreter_submit" id="interpreter_submit" value="Update Password" disabled class="btn btn-primary col-md-4" />
				<input type="reset" class="btn btn-warning col-md-2 col-md-offset-1" value="Clear" onclick="clear_fun()"/><br><br>
			</div>
			</form>
			</div>
 <?php }else{ ?>
		     <div class="col-md-6 col-md-offset-3">
		     <?php if(isset($msg) && !empty($msg)){echo $msg;} ?><br/>
		     </div>
			<div class="col-md-6 col-md-offset-3">
			 <form id="login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			     <div class="form-group"> 
			     <h3>Kindly login to proceed</h3>
			     </div>
			   <div class="form-group"> 
		    <input type="text" name="loginEmail" id="loginEmail" value="" placeholder='Email' class="form-control" required />
		    </div>
		    <div class="form-group"> 
				<input type="password" name="loginPass" id="loginPass" placeholder="Password" required  class="form-control"/>
			</div>
			<div class="form-group"> 
				<input type="submit" class="btn btn-primary col-md-4" name="login" value="Log in" />
				<input type="reset" class="btn btn-warning col-md-2 col-md-offset-1" value="Clear" />
			</div>
			<div class="form-group col-md-12 row">
				<br> 
                <strong><a class="text-danger" href="javascript:void(0)" onclick="alert('Contact LSUK at Ph: 01173290610 ');" rel="submenu">Forgot Your Password</a></strong>
            </div>
			</form>
			</div>
 <?php } ?>
</section>
   <hr>
        <section>
     	<!-- begin clients -->
       <?php //include 'source/our_client.php'; ?>
        <!-- end clients -->   
 </section>
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
</body>
<script>
    function clear_fun(){
        $('#error_pass').html('');
        $('#error_confirm').html('');
    }
    function confirmpass(){
    if (document.getElementById("upassword").value!=document.getElementById("cpassword").value) {
      $('#error_confirm').css('display','block');
      document.getElementById("cpassword").focus();
      document.getElementById("interpreter_submit").disabled=true;
      return false;
    }else{
      $('#error_confirm').css('display','none');
      document.getElementById("interpreter_submit").disabled=false;
    }
  }
  function showpassword() {
    var x = document.getElementById("upassword");
    var y = document.getElementById("cpassword");
    if (x.type === "password" || y.type === "password") {
      x.type = "text";y.type = "text";
    } else {
      x.type = "password"; y.type = "password";
    }
  }
  re1 = /^(?=.*[a-zA-Z])/;
    re2 = /^(?=.*[A-Z])/;
    re3 = /^(?=.*\d)/;
    re4 = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/;
    re5=/^(?=.*[_!@#$^&*-])/;
  function p(){
    $("#msg_note").css('display','block');
    var myInput = $("#upassword").val();

    if(!re1.test(myInput)) {
        $( "#list-group li:eq(0)" ).addClass( "text-red");
        $( "#list-group li:eq(0)" ).find("i").removeClass( "fa-check-circle");
        $("#upassword").focus();
        document.getElementById("interpreter_submit").disabled=true;
        return false;
    }else{
        $( "#list-group li:eq(0)" ).removeClass( "text-red");
        $( "#list-group li:eq(0)" ).addClass( "text-success");
        $( "#list-group li:eq(0)" ).find("i").addClass( "fa-check-circle");
    }

    if(!re3.test(myInput)) {
        $( "#list-group li:eq(2)" ).addClass( "text-red");
        $( "#list-group li:eq(2)" ).find("i").removeClass( "fa-check-circle");
        $("#upassword").focus();
        document.getElementById("interpreter_submit").disabled=true;
        return false;
    }else{
        $( "#list-group li:eq(2)" ).removeClass( "text-red");
        $( "#list-group li:eq(2)" ).addClass( "text-success");
        $( "#list-group li:eq(2)" ).find("i").addClass( "fa-check-circle");
    }

}
function p1(){
    var myInput = $("#upassword").val();


    if(!re2.test(myInput)) {
        $( "#list-group li:eq(1)" ).addClass( "text-red");
        $( "#list-group li:eq(1)" ).find("i").removeClass( "fa-check-circle");
        $("#upassword").focus();
        document.getElementById("interpreter_submit").disabled=true;
        return false;
    }else{
        $( "#list-group li:eq(1)" ).removeClass( "text-red");
        $( "#list-group li:eq(1)" ).addClass( "text-success");
        $( "#list-group li:eq(1)" ).find("i").addClass( "fa-check-circle");
    }
}
function p2(){
    var myInput = $("#upassword").val();


     if(!re3.test(myInput)) {
        $( "#list-group li:eq(2)" ).addClass( "text-red");
        $( "#list-group li:eq(2)" ).find("i").removeClass( "fa-check-circle");
        $("#upassword").focus();
        document.getElementById("interpreter_submit").disabled=true;
        return false;
    }else{
        $( "#list-group li:eq(2)" ).removeClass( "text-red");
        $( "#list-group li:eq(2)" ).addClass( "text-success");
        $( "#list-group li:eq(2)" ).find("i").addClass( "fa-check-circle");
    }
}
function p3(){
    var myInput = $("#upassword").val();


    if(myInput.length<8) {
        $( "#list-group li:eq(3)" ).addClass( "text-red");
        $( "#list-group li:eq(3)" ).find("i").removeClass( "fa-check-circle");
        $("#upassword").focus();
        document.getElementById("interpreter_submit").disabled=true;
        return false;
    }else{
        $( "#list-group li:eq(3)" ).removeClass( "text-red");
        $( "#list-group li:eq(3)" ).addClass( "text-success");
        $( "#list-group li:eq(3)" ).find("i").addClass( "fa-check-circle");
    }
}
</script>
</html>