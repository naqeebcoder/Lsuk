<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include 'class.php'; 
if(isset($_POST['submit'])){
    $table='login';$dbemail=$acttObj->uniqueFun($table,'email',$_POST['email']);
if($dbemail==0){$edit_id= $acttObj->get_id($table);}
}?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Sign Up Form</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<?php include'ajax_uniq_fun.php'; ?>
</head>
<body>
<div class="container">
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" autocomplete="off">
  <h3 class="text-center">Sign Up Form</h3>
    <div class="bg-info col-xs-12 form-group"><h4>Sign up Details</h4></div>
    <div class="form-group col-md-4 col-sm-6">
      <label>Name * </label>
      <input name="name" type="text" placeholder='' required='' id="name" class="form-control valid"/> 
      </div>
     <div class="form-group col-md-4 col-sm-6">
      <label>Email *</label>
      <input name="email" type="text"  placeholder='' required='' id="email" onchange="uniqueFun(this.value,'login','email',$(this).attr('id') );"  class="form-control"/>
       </div>
            <div class="form-group col-md-4 col-sm-6">
     <label>Passport # * </label>
      <input name="pasport" type="text" placeholder='' required='' id="pasport"  class="form-control"/> </div>
        <div class="form-group col-md-4 col-sm-6">
      <label>Password * </label>
      <input name="pass" type="password" placeholder='' required='' id="pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"  onchange="form.repass.pattern = this.value;" class="form-control"/>
      <?php if(isset($_POST['submit']) && $dbemail==0){$c1=$_POST['name']; $acttObj->editFun($table,$edit_id,'name',$c1);} ?>
      <?php if(isset($_POST['submit'])  && $dbemail==0){$c2=$_POST['pass']; $acttObj->editFun($table,$edit_id,'pass',$c2);} ?>
     </div>
            <div class="form-group col-md-4 col-sm-6">
      <label>Confirm Password * </label>
      <input name="repass" type="password" placeholder='' required='' id="repass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"  class="form-control"/>
      <?php if(isset($_POST['submit'])  && $dbemail==0){$c1=$_POST['name']; $acttObj->editFun($table,$edit_id,'name',$c1);} ?>
      <?php if(isset($_POST['submit'])  && $dbemail==0){$c1=$_POST['email']; $acttObj->editFun($table,$edit_id,'email',$c1);} ?>
     </div>
            <div class="form-group col-md-4 col-sm-6">
      <label>Role Name *</label>
      <select name="prv" class="form-control">
      <option>Finance</option>
      <option>Operator</option>
      </select>
      <?php if(isset($_POST['submit'])  && $dbemail==0){$c1=$_POST['pasport']; $acttObj->editFun($table,$edit_id,'pasport',$c1);} ?>
      <?php if(isset($_POST['submit'])  && $dbemail==0){
      $prv=$_POST['prv']; $acttObj->editFun($table,$edit_id,'prv',$prv);
      }
      if(isset($_POST['submit'])){
          $roleid=$prv=='Operator'?'1832':'1833';
          $roleid = $prv=='Test' ? '1838' : $roleid;
          $counter=$acttObj->read_specific("count(userid) as counter","userrole","userid=".$edit_id." and roleid=".$roleid)['counter'];
          if($counter==0){
              if($dbemail==0){
                $acttObj->insert("userrole",array('userid'=>$edit_id,'roleid'=>$roleid,'dated'=>date('Y-m-d')));
              }
          }
      }?>
     </div>
        <div class="form-group col-md-4 col-sm-6">
        <label>Account Status</label>
      <select name="Temp" required class="form-control">
      <option value="0">Normal</option>
      <option value="1">Temporary</option>
      </select>
      <?php if(isset($_POST['submit'])){
          $Temp_post=$_POST['Temp']; 
      $acttObj->editFun($table,$edit_id,'Temp',$Temp_post);
      } ?>
     </div>
    <div class="form-group col-md-4 col-sm-6">
    <br><button type="submit" style="font-weight:bold;font-size:16px;" name="submit" class="btn btn-primary" onclick="return formSubmit(); return false">Submit &raquo;</button>
  </div>
</form>
  </div>
<?php if(isset($_POST['submit'])  && $dbemail==0){
$acttObj->editFun($table,$edit_id,'edited_by',$_SESSION['UserName']);
  $acttObj->editFun($table,$edit_id,'edited_date',date("Y-m-d H:i:s"));
  $acttObj->editFun($table,$edit_id,'token',strtotime(date("Y-m-d H:i:s")).mt_rand(10000,99999));
  $acttObj->new_old_table('hist_'.$table,$table,$edit_id);?>
  <script>alert('New account created successfully.');
  window.onunload = refreshParent;
  function refreshParent() {window.opener.location.reload();}
  window.close();</script>
<?php } ?>
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
</script>
</html>