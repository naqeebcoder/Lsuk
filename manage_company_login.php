<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
error_reporting(0);
if(empty($_SESSION['cust_UserName'])){
    echo '<script>window.location="index.php";</script>';
}
include 'source/db.php';
include 'source/class.php';
include 'source/setup_email.php';
$id= @$_GET['id'];
$table = "company_login";
$action= @$_GET['action'];
$row = $acttObj->read_specific("comp_reg.name,company_login.*","company_login,comp_reg","company_login.company_id=comp_reg.id AND company_login.id=".$id);?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Manage Company Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
<script>
function refreshParent(){
    window.opener.location.reload();
}
</script>
</head>
<body>
    <div class="container">
    <?php if($action=="add" && $_SESSION['role']=='admin'){ ?>
    <form action="" method="post" class="register" id="signup_form" name="signup_form"><br><br>
        <label class="text-center">Add company login credentials</label><br>
        <div class="bg-info col-xs-12 form-group"><h4><?php echo "Select company from dropdown"; ?></h4></div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Company *</label>
        <select id="orgName" name="orgName" required='' class="form-control">
                    <?php 
					$sql_opt="SELECT comp_reg.id,comp_reg.abrv,comp_reg.name FROM comp_reg where comp_reg.status <> 'Company Seized trading in' and comp_reg.status <> 'Company Blacklisted' and comp_reg.deleted_flag=0 and comp_reg.is_temp=0 and comp_reg.abrv NOT IN (SELECT DISTINCT orgName FROM company_login) ORDER BY comp_reg.name ASC";
					$result_opt=mysqli_query($con,$sql_opt);
					$options="";
					while ($row_opt=mysqli_fetch_array($result_opt)) {
						$company_id=$row_opt["id"];
						$orgName=$row_opt["abrv"];
						$name=$row_opt["name"];
						$options.="<option value='".$company_id.'|'.$orgName."'>".$name. '</option>';}
					?>
                    <option value="">--Select--</option>
                    <?php echo $options; ?>
                  </select>
                  </div><div class="form-group col-md-4 col-sm-6">
        <label>Email *</label>
        <input name="email" type="text" id="unique" placeholder='Write Login Email' required='' class="form-control"/>
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Password * </label>
        <input name="password" type="password" id="pass"  onchange="form.repass.pattern = this.value;" placeholder='Add a password' required='' class="form-control pass"/>
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Confirm Password * </label>
        <input name="repass" type="password" id="repass" placeholder='Confirm password' required='' class="form-control pass"/>
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <br><input type="checkbox" id="checkbox"/> Show password
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <br><button type="submit" name="btn_add" class="btn btn-primary">Submit &raquo;</button>
    </div>
    </form>
    <?php }else{
    if(empty($row['id'])){ ?>
            <center><h3>Coudn't find this record!</h3><button class="btn btn-danger" type="button" onclick="window.close();">Close</button></center>
        <?php }else{
     if($action=="edit"){ ?>
    <form action="" method="post" class="register" id="signup_form" name="signup_form"><br><br>
        <label class="text-center">Update company login credentials</label><br>
        <div class="bg-info col-xs-12 form-group"><h4><?php echo $row['name']; ?></h4></div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Email *</label>
        <input name="email" type="text" id="unique" value="<?php echo $row['email']; ?>" placeholder='' required='' class="form-control"/>
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Password * </label>
        <input name="password" type="password" id="pass"  onchange="form.repass.pattern = this.value;" 
            value="<?php echo $row['paswrd']; ?>" placeholder='' required='' class="form-control pass"/>
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Confirm Password * </label>
        <input name="repass" type="password" id="repass" value="<?php echo $row['paswrd']; ?>" placeholder='' required='' class="form-control pass"/>
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <br><input type="checkbox" id="checkbox"/> Show password
        </div>
        <div class="form-group col-md-4 col-sm-6">
        <br><button type="submit" name="btn_edit" class="btn btn-primary">Submit &raquo;</button>
    </div>
    </form>
    <?php }
    if($action=="delete"){ ?>
        <div align="center">
            <h3>Trash: <span class="label label-danger"><?php echo $row['name']; ?></span></h3><br/>
            <form action="" method="post">
            <h3>Are you sure to <span class="text-danger"><b>Trash</b></span> this record ?</h3>
            <input type="submit" class="btn btn-primary" name="btn_delete" value="Yes >" />&nbsp;&nbsp;<input class="btn btn-warning" type="submit" name="no" value="No" />
            </form>
        </div>
    <?php }
    if($action=="undo"){ ?>
        <div align="center">
            <h3>Restore: <span class="label label-info"><?php echo $row['name']; ?></span></h3><br/>
            <form action="" method="post">
            <h3>Are you sure to <span class="text-danger"><b>Restore</b></span> this record ?</h3>
            <input type="submit" class="btn btn-primary" name="btn_restore" value="Yes >" />&nbsp;&nbsp;<input class="btn btn-warning" type="submit" name="no" value="No" />
            </form>
        </div>
    <?php }
        }
    } ?>
</div>
<?php if(isset($_POST['btn_edit'])){
  $acttObj->update("company_login",array("email"=>$_POST['email'],"paswrd"=>$_POST['password']),array("id"=>$id));
  ?>
    <script>
    alert('Account updated Successfuly !');
    window.onunload = refreshParent;
    window.close();</script>
    <?php }
    if(isset($_POST['btn_add'])){
    $extract_data=explode('|',$_POST['orgName']);
    $acttObj->insert("company_login",array("company_id"=>$extract_data[0],"orgName"=>$extract_data[1],"email"=>$_POST['email'],"paswrd"=>$_POST['password'],"dated"=>date('Y-m-d')));
    $subject = "Welcome to LSUK - Account Details";
    $message = "<p>Hi</p><p>Please use the Below credentials to login into your LSUK portal:</p><p>Email:".$_POST['email']."<br />Password: ".$_POST['password']."</p><p>Best Regards,<br /><br /></p><p><strong>LSUK Limited</strong></p><p>Landline: 01173290610<br />Mobile: 07915177068<br />Office Address: Suite 3 Davis House<br />Lodge Causeway Trading estate<br />Lodge Causeway - Fishponds<br />Bristol BS16 3JB<br />Opening Hours: Monday - Friday 09AM to 5PM</p>";
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = setupEmail::EMAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = setupEmail::INFO_EMAIL;
        $mail->Password   = setupEmail::INFO_PASSWORD;
        $mail->SMTPSecure = setupEmail::SECURE_TYPE;
        $mail->Port       = setupEmail::SENDING_PORT;
        $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
        $mail->addAddress($_POST['email']);
        $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
        $mail->ClearAllRecipients();
    } catch (Exception $e) { ?>
        <script>
            alert("Message could not be sent! Mailer library error."
                <?php echo $e->getMessage() ?>);
        </script>
<?php   }
    ?>
    <script>
    alert('Account added Successfuly !');
    window.onunload = refreshParent;
    window.close();</script>
    <?php }
if(isset($_POST['btn_delete'])){
  $acttObj->editFun($table,$id,'deleted_flag',1);?>
  <script>
    window.onunload = refreshParent;
    window.close();</script>
    <?php }
if(isset($_POST['btn_restore'])){
  $acttObj->editFun($table,$id,'deleted_flag',0);?>
  <script>
    window.onunload = refreshParent;
    window.close();</script>
    <?php }
    if(isset($_POST['no'])){
    echo "<script>window.close();</script>";
    };
?>
</body>
<script>
$(".valid").bind('keypress paste',function (e) {
  var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (!regex.test(str)) {
    e.preventDefault();
    return false;
  }
});
var $vp = $('#checkbox');
$vp.on('click', function() {
    var $target = $('.pass');
  if ($target.attr('type') == "password") {$target.attr('type','text');}
  else {$target.attr('type','password');}
});
</script>
</html>