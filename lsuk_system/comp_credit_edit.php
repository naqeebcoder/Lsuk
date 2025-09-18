<?php 
include 'db.php';
session_start();
include 'class.php';
$table='comp_credit';
$edit_id= @$_GET['edit_id'];
$rem_amount= @$_GET['rem'];
if(isset($_POST['submit'])){
    $post_orgName=$_POST['orgName'];
    $acttObj->editFun($table,$edit_id,'orgName',$post_orgName);
    $post_credit=$_POST['credit'];
    $acttObj->editFun($table,$edit_id,'credit',$post_credit); 
    $post_porder=$_POST['porder'];
    $acttObj->editFun($table,$edit_id,'porder',$post_porder);
    $post_comments=$_POST['comments'];
    $acttObj->editFun($table,$edit_id,'comments',$post_comments);
    $array_porder_for=implode(",",$_POST['porder_for']);
    if(!empty($array_porder_for)){
      $acttObj->editFun($table,$edit_id,'porder_for',$array_porder_for);
    }
    $acttObj->editFun($table,$edit_id,'mode','Credit');
    $acttObj->editFun($table,$edit_id,'credit_date',date("Y-m-d"));
    $acttObj->editFun($table,$edit_id,'edited_by',$_SESSION['UserName']);
    $acttObj->editFun($table,$edit_id,'edited_date',date("Y-m-d H:i:s"));
    $acttObj->new_old_table('hist_'.$table,$table,$edit_id);
}
$query="SELECT * FROM $table where id=$edit_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$rowID=$row['id'];
$orgName=$row['orgName'];
$credit=$row['credit'];
$porder=$row['porder'];
$comments=$row['comments'];
$porder_for=$row['porder_for'];
$put_porder_for=!empty($porder_for)?"AND id NOT IN ($porder_for)":""; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Update Purchase Order</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.multiselect {min-width: 300px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;} .credit_form{text-align: center;width: 70%; margin: 0 auto; }</style> 
<script src="js/debug.js"></script>
<script>
function company_change(elSel){
  var opt = elSel.options[elSel.selectedIndex];
  var strVal=opt.value;
  var strTxt=opt.text;
  window.location.href="comp_credit_edit.php?orgname="+encodeURIComponent(strVal)+"&txt="+encodeURIComponent(strTxt);
}
function formSubmit(){
  return true;
}
</script>
<?php include 'ajax_uniq_fun.php'; ?>
</head>
<body>
<form action="" method="post" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
<div class="container-fluid">
<div class="row">
<div class="credit_form">
  <h1 class="text-center">Update Purchase Order</h1>
    <div class="form-group col-md-12 col-sm-12">
      <label>Company * </label><br>
      <select  id="orgName" name="orgName"  class="form-control multi_class">
      <?php $sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
          $result_opt=mysqli_query($con,$sql_opt);
          $options="";
          while ($row_opt=mysqli_fetch_array($result_opt)) {
              $code=$row_opt["abrv"];
              $name_opt=$row_opt["name"];
              if($orgName==$code){
                $fulname=$name_opt;$abrivation=$code;
              }
              $options.="<option value='$code'>".$name_opt."</option>";}
          ?>
					<option value="<?php echo $abrivation ?>"><?php echo $fulname ?></option>
          <option value="0">--Select--</option>
          <?php echo $options; ?>
        </select>
	</div>
  <?php if(isset($orgName)){ ?>
  <div class="form-group col-md-12 col-sm-12">
      <label>Company References </label><br>
      <select id="porder_for" name="porder_for[]"  multiple="multiple"  class="form-control multi_class">
      <?php $get_references=$acttObj->read_all("id,reference","comp_ref","company='".$orgName."' $put_porder_for ORDER BY reference ASC");
              if(!empty($put_porder_for)){
                $arr_references=explode(',',$porder_for);
                for($i=0;$i<count($arr_references);$i++){
                    echo "<option selected value='$arr_references[$i]'>".$acttObj->read_specific("reference","comp_ref","id=".$arr_references[$i])['reference']."</option>";
                }
              }
              while($row_ref=$get_references->fetch_assoc()) {
                  echo "<option value=".$row_ref["id"].">".$row_ref["reference"]."</option>";
              } ?>
      </select>
      </div>
      <?php } ?>
  <div class="form-group col-md-12 col-sm-12">
      <label>Total Credited &pound; </label>
      <input class="form-control" name="credit" type="text" value="<?php echo $credit ; ?>" readonly />
  </div>
  <div class="form-group col-md-12 col-sm-12">
      <label><strong><em>Remaining</em></strong></label>
      <input class="form-control" name="rem_amount" type="text" id="rem_amount" readonly value="<?php echo $rem_amount; ?>" />
	</div>
    <div class="form-group col-md-12 col-sm-12">
      <label>Add Credit &pound; </label>
      <input class="form-control" name="credit" type="text" placeholder='' id="credit" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" />
    </div>
    <div class="form-group col-md-12 col-sm-12">
	    <label><strong><em>Purchase Order No.</em></strong></label>
      <input class="form-control" name="porder" type="text" id="porder"  placeholder='' value="<?php echo $porder ; ?>" onBlur="uniqueFun(this.value,'comp_credit','porder',$(this).attr('id'),'editFlag',<?php echo $rowID; ?> );" />
    </div>
    <div class="form-group col-md-12 col-sm-12">
        <h4>Notes if Any 1000 alphabets</h4>
        <textarea name="comments" cols="51" rows="5" class="form-control"><?php echo $comments ; ?></textarea>
	</div>
  <div class="row"></div>
    <div class="form-group col-md-12 col-sm-12">
      <button class="btn btn-primary btn-lg" type="submit" name="submit" onclick="return formSubmit(); return false">Submit &raquo;</button>
	</div>
  </div>
</div>
</div>
</form>
</body>
</html>
<?php if(isset($_POST['submit'])){ ?>
    <script>
    alert('Successful!');
    window.onunload = refreshParent;
    function refreshParent() {window.opener.location.reload();}</script>
<?php } ?>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
$(function() {
	    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
});
</script>