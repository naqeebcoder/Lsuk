<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include'db.php';
if(session_id() == '' || !isset($_SESSION)){ session_start();}
include'class.php';
$table='comp_credit'; 
$invoiceno="";
$remaining="";
if(isset($_POST['submit'])){
    $post_orgName=$_POST['orgName'];
    $post_porder=$_POST['porder'];
    $ch_po='';
    $ch_po = $acttObj->read_specific("id",$table,"porder='$post_porder'")['id'];
    if(empty($ch_po)){
      $edit_id= $acttObj->get_id($table);
      $acttObj->editFun($table,$edit_id,'orgName',$post_orgName);
      $post_credit=$_POST['credit'];
      $acttObj->editFun($table,$edit_id,'credit',$post_credit); 
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
      echo "<script>alert('Successfull');window.location = window.location.href.split('?')[0];</script>";
    }else{
      echo "<script>alert('Purchase Order # $post_porder Already Exists!');</script>";
    }
}
if (isset($_GET["orgname"])){
	$get_company=$_GET["orgname"];
    $query_r="SELECT  sum(comp_credit.credit) - sum(comp_credit.debit) as remaining,sum(comp_credit.debit) debit,comp_credit.debit_date 
    FROM comp_credit where comp_credit.orgName like '$get_company%' order by comp_credit.id desc LIMIT 1";
  $result_r = mysqli_query($con,$query_r);
  while($row_r = mysqli_fetch_array($result_r)){
      $remaining=$row_r['remaining'];
      $debit=$row_r['debit'];
      $debit_date=$row_r['debit_date'];
  }	
  /*if(!isset($_POST['submit'])){
    $new_nmbr=$acttObj->get_id('invoice');
    $newid=$new_nmbr;
    $new_nmbr = str_pad($new_nmbr, 5, "0", STR_PAD_LEFT);
    $invoice_ful= date("my").$new_nmbr;
    $acttObj->editFun('invoice',$newid,'invoiceNo',$invoice_ful);
    $invoiceno=$invoice_ful;
  }*/
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Add Purchase Order Credit</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.multiselect {min-width: 300px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;} .credit_form{text-align: center;width: 70%; margin: 0 auto; }</style>
<script src="js/debug.js"></script>
<script>
function company_change(elSel){
  var opt = elSel.options[elSel.selectedIndex];
  var strVal=opt.value;
  var strTxt=opt.text;
  window.location.href="comp_credit.php?orgname="+encodeURIComponent(strVal)+"&txt="+encodeURIComponent(strTxt);
}
function formSubmit(){
  return true;
}
</script>
<?php include'ajax_uniq_fun.php'; ?>
</head>
<body>
<form action="" method="post" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
<div class="container-fluid text-center">
<div class="row">
  <div class="credit_form">
  <h1 class="text-center">Add New Purchase Order</h1>
    <div class="form-group col-md-12 col-sm-12">
      <label>Company * </label><br>
      <select onchange="company_change(this);" id="orgName" name="orgName"  class="form-control multi_class">
      <?php $sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
        $result_opt=mysqli_query($con,$sql_opt);
        $options="";
        while ($row_opt=mysqli_fetch_array($result_opt)) {
            $code=$row_opt["abrv"];
            $name_opt=$row_opt["name"];
            $options.="<option value='$code'>".$name_opt."</option>";
        }
        if (isset($_GET["orgname"]))
        {
          echo "<option value='".$_GET["orgname"]."'>".$_GET["txt"]."</option>";
        }else{
          echo "<option value='0'>--Select--</option>";
        }
        echo $options; ?>
      </select>
	</div>
  <?php if(isset($get_company)){ ?>
  <div class="form-group col-md-12 col-sm-12">
      <label>Company References </label><br>
      <select id="porder_for" name="porder_for[]"  multiple="multiple"  class="form-control multi_class">
      <?php $get_references=$acttObj->read_all("id,reference","comp_ref","company='".$get_company."'");
            while($row_ref=$get_references->fetch_assoc()) {
                echo "<option value=".$row_ref["id"].">".$row_ref["reference"]."</option>";
            } ?>
      </select>
      </div>
      <?php } ?>
    <div class="form-group col-md-12 col-sm-12">
      <label>Credit &pound;* </label>
      <input class="form-control" name="credit" type="text" placeholder='' required='' id="credit" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" />
	</div>
    <div class="form-group col-md-12 col-sm-12">
	<label><strong><em>Purchase Order No.</em></strong></label>
      <input class="form-control" name="porder" type="text" id="porder"  placeholder='' required='' value="<?php echo $invoiceno; ?>"
        onBlur="uniqueFun(this.value,'comp_credit','porder',$(this).attr('id') );"/>    
	</div>
    <div class="form-group col-md-12 col-sm-12">
        <h4>Notes if Any 1000 alphabets</h4>
        <textarea name="comments" cols="51" rows="5" class="form-control"></textarea> 
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
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
$(function() {
	    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
</script>