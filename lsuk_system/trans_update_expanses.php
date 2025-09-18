<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 

if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 
include'db.php'; 
include'class.php'; 

$update_id=$_GET['update_id']; 
$table='translation';
$query="SELECT $table.*,interpreter_reg.name  FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
 where $table.id=$update_id";			
$result = mysqli_query($con,$query);

$row = mysqli_fetch_array($result);
  $bookinType=$row['bookinType'];
  $numberUnit=$row['numberUnit'];
  $rpU=$row['rpU'];
  $otherCharg=$row['otherCharg'];
  $intrpName=$row['intrpName'];
  $total_charges_interp=$row['total_charges_interp'];
  $dueDate=$row['dueDate'];$deduction=$row['deduction'];
  $admnchargs=$row['admnchargs'];
  $exp_remrks=$row['exp_remrks'];
  $ni_dedu=$row['ni_dedu'];
  $tax_dedu=$row['tax_dedu'];
  $interp_name=$row['name'];
  $asignDate=$row['asignDate'];
  $int_vat=$row['int_vat'];
  $vat_no_int=$row['vat_no_int'];
  $interp_rpu=$acttObj->unique_data('interpreter_reg','rpu','id',$intrpName); 
if($row['asignDate']>date('Y-m-d')){
    $problem_hours=1;
    $problem_msg='Assignment Date : <b class="text-danger">'.$row['asignDate'].'</b><br><br>This job is not completed yet! Thank you';
}else if($row['deleted_flag']==1 || $row['order_cancel_flag']==1){
    $problem_hours=1;
    $problem_msg='This job is in processing mode! Thank you';
}else{
    $problem_hours=0;
    $problem_msg='';
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <title>Translation Expenses - Translation Interpreting</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<head>
          <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('numberUnit').value);	
		var rateHour = parseFloat(document.getElementById('rpU').value);
		var x = rateHour * hoursWorkd;
		var deduction = parseFloat(document.getElementById('deduction').value);	
		var admnchargs = parseFloat(document.getElementById('admnchargs').value);
		
		var otherCharges = parseFloat(document.getElementById('otherCharg').value);
		
		total_charges_interp.value=(parseFloat(x+otherCharges+admnchargs)-parseFloat(deduction)).toFixed(2);
		}
        function checkDec(el){
         var ex = /^[0-9]+\.?[0-9]*$/;
         if(ex.test(el.value)==false){
           el.value = 0;
           el.select();
           calcInterp();
          }
        }
    function fun_vat_no(){
        var int_vat=document.getElementById("int_vat").value;
        var vat_no_int=document.getElementById("vat_no_int");
        var div_vat_no=document.getElementById("div_vat_no");
        if (!isNaN(int_vat) && int_vat!=0){
            div_vat_no.style.display='inline';
            vat_no_int.setAttribute("required", "required");
        }else{
            div_vat_no.style.display='none';
            vat_no_int.removeAttribute("required", "required");
        }
    }
        
        </script>
    </head>
<body>
<?php if($problem_hours==1){?>
    <center><br><br><h3><?php echo isset($problem_msg) && !empty($problem_msg)?$problem_msg:''; ?></h3>
						<br><br><a class="btn btn-primary" href="javascript:void(0)" onclick="window.close();"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a></center>
<?php }else{ ?>
<form action="" method="post" class="register">
    <div class="col-xs-12 text-center"><h4>Face To Face  - Update Interpreter Expenses For <span style="color:#F00;"><?php echo $interp_name. ' ( '.$asignDate.' )'; ?></span></h4>
          </div>
          <div class="bg-info col-xs-12 form-group"><h4>Fixed Rate or Per Word Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
          </div>
      <div class="form-group col-md-3 col-sm-6">
    <p>Units (Word Count) </p>
         <input class="form-control" name="numberUnit" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="numberUnit" style="border:1px solid #CCC" required='' value="<?php echo $numberUnit; ?>"oninput="calcInterp()" onkeyup="checkDec(this);" />
          </div>
      <div class="form-group col-md-3 col-sm-6">
      <p> Rate per Unit </p>
      <input name="rpU" type="text" id="rpU" class="form-control" required='' value="<?php if($rpU!=0){echo $rpU ;}else{echo $interp_rpu;} ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php if(isset($_POST['submit'])){$c1=$_POST['numberUnit']; $acttObj->editFun($table,$update_id,'numberUnit',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['rpU']; $acttObj->editFun($table,$update_id,'rpU',$c1);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6">
  <p> Any other Charges (If Applicable) </p>
      <input class="form-control" name="otherCharg" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCharg" required='' value="<?php echo $otherCharg; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
          </div>
      <div class="form-group col-md-3 col-sm-6">
<p>Additional Payment (If Applicable) 
    </p>
                    <input class="form-control" name="admnchargs" type="text" id="admnchargs"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $admnchargs ?>" placeholder=''oninput="calcInterp()" onkeyup="checkDec(this);" />
                <?php if(isset($_POST['submit'])){$c14=$_POST['admnchargs'];$acttObj->editFun($table,$update_id,'admnchargs',$c14);} ?>
<?php if(isset($_POST['submit'])){$c1=$_POST['otherCharg']; $acttObj->editFun($table,$update_id,'otherCharg',$c1);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6">
  <p>Due Date for Bill Payment
          </p>
               <input class="form-control" name="dueDate" type="date" id="dueDate" value="<?php echo $dueDate ?>"/>
          </div>
      <div class="form-group col-md-3 col-sm-6">
              <p>Deduction (If Applicable)  </p>
               <input class="form-control" name="deduction" type="text" id="deduction"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $deduction ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
               <?php if(isset($_POST['submit'])){$c15=$_POST['deduction'];$acttObj->editFun($table,$update_id,'deduction',$c15);} ?>
<?php if(isset($_POST['submit'])){$c9=$_POST['dueDate'];$acttObj->editFun($table,$update_id,'dueDate',$c9);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6">
      <p>National Insurance Deduction <i class="glyphicon glyphicon-question-sign" title="(If Applicable)"></i></p>
                    <input class="form-control" name="ni_dedu" type="text" id="ni_dedu"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $ni_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
              <?php if(isset($_POST['submit'])){$c15=$_POST['ni_dedu'];$acttObj->editFun($table,$update_id,'ni_dedu',$c15);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6">
      <p>Tax Deduction (If Applicable) 
</p>
                    <input class="form-control" name="tax_dedu" type="text" id="tax_dedu"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $tax_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
              <?php if(isset($_POST['submit'])){$c15=$_POST['tax_dedu'];$acttObj->editFun($table,$update_id,'tax_dedu',$c15);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6">
           <p style="color:#F00">Current VAT % </p>
                  <input class="form-control" name="int_vat" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="int_vat" value="<?php echo $int_vat?:0;?>" placeholder='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);" required=''/>
              <?php if(isset($_POST['submit'])){$int_vat_post=$_POST['int_vat']; $acttObj->editFun($table,$update_id,'int_vat',$int_vat_post);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6" <?php if(!empty($int_vat) && $int_vat!=0){ echo 'style="display:inline"';}else{ echo 'style="display:none"';} ?> id="div_vat_no">
           <p style="color:#F00">VAT Number (if any) </p>
                  <input class="form-control" name="vat_no_int" type="text" id="vat_no_int" value="<?php echo $vat_no_int;?>" placeholder=''/>
              <?php if(isset($_POST['submit'])){$vat_no_post=$_POST['vat_no_int']; $acttObj->editFun($table,$update_id,'vat_no_int',$vat_no_post);} ?>
          </div>
      <div class="form-group col-md-3 col-sm-6">
<p><b>Total</b> </p>
                  <input class="form-control" name="total_charges_interp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_interp" value="<?php echo $total_charges_interp ?>" readonly="readonly"/>
                  <?php if(isset($_POST['submit'])){$c10=$_POST['total_charges_interp'];$acttObj->editFun($table,$update_id,'total_charges_interp',$c10);} ?>
          </div>
      <div class="form-group col-sm-8">
              <textarea class="form-control" placeholder="Notes (if Any) 1000 characters" class="form-control" name="exp_remrks" rows="3" id="exp_remrks"><?php echo $exp_remrks ?></textarea>
              <?php if(isset($_POST['submit'])){$c1=$_POST['exp_remrks']; $acttObj->editFun($table,$update_id,'exp_remrks',$c1);} ?>
          </div>
          <div class="form-group col-md-4 col-sm-6">
                   	    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" id="btn_submit_expense">Submit &raquo;</button></p></div>
</form>
<?php if(isset($_POST['submit'])){
    if($_SESSION['Temp']==1){
        $acttObj->editFun($table,$update_id,'is_temp',1);
    }
  $data=($_POST['numberUnit']*$_POST['rpU'])+$_POST['otherCharg'];
  $acttObj->editFun($table,$update_id,'total_charges_interp',$data);
  $acttObj->editFun($table,$update_id,'hrsubmited',ucwords($_SESSION['UserName']));
  $acttObj->editFun($table,$update_id,'interp_hr_date',$misc->sys_date_db());?>
  <script>alert("Job's interpreter expenses has been updated successfully.");
  window.onload = function()
  {
    calcInterp();
  };

  window.onunload = refreshParent;
  function refreshParent() {
    window.opener.location.reload();
  }
</script>
<?php } 
} ?>
</body>
</html>