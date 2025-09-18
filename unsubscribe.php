<?php 
    if(session_id() == '' || !isset($_SESSION))
    {
        session_start();
    } 
    error_reporting(0);
?> 

<?php 
include 'source/db.php';
include 'source/class.php';
include_once ('source/function.php');

//$name=@$_GET['name']; 
//$gender=@$_GET['gender']; 
//$city=@$_GET['city'];
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
if($UserNam && $Pswrd)
{
$query="SELECT count(*) num,id, name,contactNo, email,code,gender,address FROM interpreter_reg where  email='$UserNam' AND password='$Pswrd'";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$flag=$row['num'];$UserName=$row['name'];$id=$row['id'];$email=$row['email'];$contactNo=$row['contactNo'];$gender=$row['gender'];$interp_code=$row['code'];$address=$row['address'];}
}
if($flag==0){echo "Wrong!";}
if($flag==1){
$_SESSION['web_UserName']=$UserName;
$_SESSION['web_userId']=$id;	
$_SESSION['email']=$email;
$_SESSION['web_contactNo']=$contactNo;
$_SESSION['web_address']=$address;
$_SESSION['gender']=$gender;			
$_SESSION['interp_code']=$interp_code;	
$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//header('location:$url'); 
$result_url="https://lsuk.org/unsubscribe.php";
if($url!=$result_url){ echo "<script>setTimeout(function(){ window.location.href='$result_url'; }, 3500);</script>";  }else{ echo "<script>window.location.href='$result_url';</script>"; }

}
}
$interp_id=$_SESSION['web_userId'];
?>

    <!-- begin page title -->
    <section id="page-title">
    	<div class="container clearfix">
            <h1>Unsubscribe Account</h1>
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
            <section style="overflow-x: auto;">
<style>.msg{color: red;
    background: #d0c9c9;
    padding: 12px;
    text-align: center;
    font-size: 19px;line-height: 1.2em;
    } 
    .msg2{color: lime;
    background: #267294;}
    .frm{font-size: 16px;
    padding: 4px;}
    </style>
    <?php if(isset($_SESSION['web_userId'])){ ?>
      <h2>Unsubscribe yourself from getting email notifications?</h2>
 <?php }else{ ?>
<center>
<h2>Kindly login to proceed</h2>
<form id="login" method="post" action="#">
<input type="text" name="loginEmail" class="frm" id="loginEmail" value="" placeholder='Email' required /><br/><br/>
<input type="password" name="loginPass" class="frm" id="loginPass" placeholder="Password" required /><br/><br/>
<input type="submit" name="login" value="Login" class="frm" />
</form>
</center>
 <?php }
//job bid starts here
 if(isset($_SESSION['web_userId'])){
 $check_res=$acttObj->unique_data('interpreter_reg','subscribe','id',$interp_id);
 if($check_res=='0'){
 $msg="<p class='msg'>Information : you have already unsubscribed yourself.</p>";
 }else{
            $acttObj->editFun('interpreter_reg',$interp_id,'subscribe','0');
            $msg="<p class='msg msg2'>Thank you! You have successfully unsubscribed from email notifications.</p>";
  }
 }
  
 if(isset($msg) && !empty($msg)){echo $msg;}?>
            </section>
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
