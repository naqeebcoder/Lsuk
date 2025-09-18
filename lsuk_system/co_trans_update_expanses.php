<?php session_start(); include'db.php'; include'class.php'; $update_id=$_GET['update_id']; $table='translation';
$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$bookinType=$row['bookinType'];$C_numberUnit=$row['C_numberUnit']?:0;$C_rpU=$row['C_rpU']?:0;$C_otherCharg=$row['C_otherCharg'];$bookinType=$row['bookinType'];$total_charges_comp=$row['total_charges_comp'];$certificationCost=$row['certificationCost'];$proofCost=$row['proofCost'];$postageCost=$row['postageCost'];$C_numberWord=$row['C_numberWord'];$C_rpW=$row['C_rpW'];$C_admnchargs=$row['C_admnchargs'];$cur_vat=$row['cur_vat'];$proofCost=$row['proofCost'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgName=$row['orgName'];$asignDate=$row['asignDate'];
  $vat_no_comp=$row['vat_no_comp'];$chk_numberUnit=$row['numberUnit']?:0;
  //Get company requirements
  $get_comp=$acttObj->read_specific("admin_ch,admin_rate","comp_reg","abrv='".$orgName."'");
  $admin_ch=$get_comp['admin_ch'];
  $admin_rate=$get_comp['admin_rate'];?>
<?php $interp_rpu=$acttObj->unique_data('booking_type','rate','title',$bookinType)?:0 ;
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
<head>
        <title>Client Expenses - Translation</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <style>.cls_danger{background:red;border:2px solid black;color: white;font-weight: bold;}</style>
    <script src="js/jquery-1.11.3.min.js"></script>
          <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('C_numberUnit').value);	
		var rateHour = parseFloat(document.getElementById('C_rpU').value);
		var certificationCost = parseFloat(document.getElementById('certificationCost').value);	
		var proofCost = parseFloat(document.getElementById('proofCost').value);	
		var postageCost = parseFloat(document.getElementById('postageCost').value);
		var x = rateHour * hoursWorkd;
		var C_numberWord = parseFloat(document.getElementById('C_numberWord').value);	
		var C_rpW = parseFloat(document.getElementById('C_rpW').value);
		var y = C_rpW * C_numberWord;
		
		var otherCharges = parseFloat(document.getElementById('C_otherCharg').value);
				
		var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);

		total_charges_comp.value=parseFloat(y+x+otherCharges+certificationCost + proofCost+ postageCost+C_admnchargs);
		}
		function accurate_values(){
		    var actual_hours=parseFloat('<?php echo $chk_numberUnit; ?>');
		    if(parseFloat($('#C_numberUnit').val())<actual_hours){
		        $('#C_numberUnit').addClass('cls_danger');
		        $('#C_numberUnit').attr('title','Hours Worked value must be atleast '+actual_hours);
            $('#btn_submit_expense').attr("disabled","disabled");
		    }else{
		        $('#C_numberUnit').removeClass('cls_danger');
            $('#btn_submit_expense').removeAttr("disabled");
		    }
		}
        function checkDec(el){
         var ex = /^[0-9]+\.?[0-9]*$/;
         if(ex.test(el.value)==false){
           el.value = 0;
           el.select();
           calcInterp();
           accurate_values();
          }
        }
    function fun_vat_no(){
        var cur_vat=document.getElementById("cur_vat").value;
        var vat_no_comp=document.getElementById("vat_no_comp");
        var div_vat_no=document.getElementById("div_vat_no");
        if (!isNaN(cur_vat) && cur_vat!=0){
            div_vat_no.style.display='inline';
            vat_no_comp.setAttribute("required", "required");
        }else{
            div_vat_no.style.display='none';
            vat_no_comp.removeAttribute("required", "required");
        }
    }
        </script>
    </head>
<body>
<?php if($problem_hours==1){?>
    <center><br><br><h3><?php echo isset($problem_msg) && !empty($problem_msg)?$problem_msg:''; ?></h3>
						<br><br><a class="btn btn-primary" href="javascript:void(0)" onclick="window.close();"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a></center>
<?php }else{ ?>
<form action="" method="post" class="register"><div class="col-xs-12 text-center"><h4>Translation - Update Client Expenses For Invoicing: <span style="color:#F00;"><?php echo $orgName. ' ( '.$asignDate.' )'; ?></span></h4>
          </div>
          <div class="bg-info col-xs-12 form-group"><h4>Fixed Rate or Per Word Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
            </div>
            <div class="form-group col-md-4 col-sm-3">
          <p>Number of Units </p>
          <input class="form-control" name="C_numberUnit" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_numberUnit" style="border:1px solid #CCC" required='' value="<?php echo $C_numberUnit; ?>" oninput="calcInterp()" onkeyup="checkDec(this);accurate_values();" />
            </div>
            <div class="form-group col-md-4 col-sm-3">
           <p>Rate Per Unit </p>
      <input class="form-control" name="C_rpU" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_rpU" style="border:1px solid #CCC" required='' value="<?php if($C_rpU!=0){echo $C_rpU ;}else{echo $interp_rpu;} ?>"oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_numberUnit']; $acttObj->editFun($table,$update_id,'C_numberUnit',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_rpU']; $acttObj->editFun($table,$update_id,'C_rpU',$c1);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
            <p>Number of Words </p>
          <input class="form-control" name="C_numberWord" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_numberWord" style="border:1px solid #CCC" required='' value="<?php echo $C_numberWord; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-3">
           <p>Rate Per Word </p>
      <input class="form-control" name="C_rpW" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_rpW" style="border:1px solid #CCC" required='' value="<?php echo $C_rpW; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_numberWord']; $acttObj->editFun($table,$update_id,'C_numberWord',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_rpW']; $acttObj->editFun($table,$update_id,'C_rpW',$c1);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                <p>CERTIFICATION COST (If Applicable) (£) </p>
          <input class="form-control" name="certificationCost" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="certificationCost" style="border:1px solid #CCC" required='' value="<?php echo $certificationCost; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-3">
           <p>PROOFREADING COST(If Applicable) (£) </p>
      <input class="form-control" name="proofCost" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="proofCost" style="border:1px solid #CCC" required='' value="<?php echo $proofCost; ?>"oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php if(isset($_POST['submit'])){$c1=$_POST['certificationCost']; $acttObj->editFun($table,$update_id,'certificationCost',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['proofCost']; $acttObj->editFun($table,$update_id,'proofCost',$c1);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
      <p>POSTAGE COST (If Applicable) (£) </p>
      <input class="form-control" name="postageCost" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="postageCost" required=''value="<?php echo $postageCost; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php if(isset($_POST['submit'])){$c1=$_POST['postageCost']; $acttObj->editFun($table,$update_id,'postageCost',$c1);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
      <p> ANY OTHER CHARGES (If Applicable) (£) </p>
      <input class="form-control" name="C_otherCharg" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_otherCharg" required=''value="<?php echo $C_otherCharg; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
      
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_otherCharg']; $acttObj->editFun($table,$update_id,'C_otherCharg',$c1);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
   <p> ADMIN CHARGES (<?php echo $admin_ch==1?'<span class="label label-danger">Must be filled</span>':'Not Applicable'; ?>) </p>
      <input <?php if($admin_ch==1){ echo 'title="Admin charge must be filled!" style="border:1px solid red;"';} ?> class="form-control" name="C_admnchargs" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_admnchargs" required=''value="<?php echo $C_admnchargs; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-sm-3">
           <p style="color:#F00">Current VAT @ % </p>
                  <input class="form-control" name="cur_vat" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="cur_vat" value="<?php if($cur_vat==0){echo 0.2;}else{echo $cur_vat;}?>" placeholder='' required='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);"/>
                <?php if(isset($_POST['submit'])){$c20=$_POST['cur_vat'];$acttObj->editFun($table,$update_id,'cur_vat',$c20);} ?>
            </div>
            <div class="form-group col-sm-3" <?php if(!empty($cur_vat) && $cur_vat!=0){ echo 'style="display:block"';}else{ echo 'style="display:none"';} ?> id="div_vat_no">
           <p style="color:#F00">VAT Number (if any) </p>
                  <input class="form-control" name="vat_no_comp" type="text"  id="vat_no_comp" value="<?php echo $vat_no_comp;?>" placeholder='Write VAT Number'/>
      <?php 
      if(isset($_POST['submit']))
      {
        $vat_no_post=$_POST['vat_no_comp'];
        $acttObj->editFun($table,$update_id,'vat_no_comp',$vat_no_post);
      } 
      ?>
            </div>
            <div class="form-group col-sm-3">
                <p><strong><em>Purchase Order No.</em></strong></p>
                  <input class="form-control" name="porder" type="text" id="porder" value="<?php echo $porder ?>" placeholder='' readonly="readonly" />
            </div>
            <div class="form-group col-sm-3">
    <p> <b>Job Total</b> </p>
      <input class="form-control" name="total_charges_comp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_comp" value="<?php echo $total_charges_comp ?>" readonly="readonly"/>
                  <?php if(isset($_POST['submit'])){$c10=$_POST['total_charges_comp'];$acttObj->editFun($table,$update_id,'total_charges_comp',$c10);} ?>
                  <?php if(isset($_POST['submit'])){$c1=$_POST['C_admnchargs']; $acttObj->editFun($table,$update_id,'C_admnchargs',$c1);} ?>
            </div>
            <div class="form-group col-sm-8">
                    <textarea placeholder="Notes if Any 1000 alphabets" class="form-control" name="C_comments" rows="3" id="C_comments"><?php echo $C_comments; ?></textarea>
                    <?php if(isset($_POST['submit'])){$data=$_POST['C_comments'];$acttObj->editFun($table,$update_id,'C_comments',$data);} ?>
            </div>
            <div class="form-group col-md-2 col-sm-6">
                   	    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" id="btn_submit_expense">Submit &raquo;</button>
                   	    </div>
  
</form>
<?php if(isset($_POST['submit'])){
    if($_SESSION['Temp']==1){
        $acttObj->editFun($table,$update_id,'is_temp',1);
    }
    $acttObj->editFun($table,$update_id,'comp_hrsubmited',ucwords($_SESSION['UserName']));$acttObj->editFun($table,$update_id,'comp_hr_date',$misc->sys_date_db()); ?>
<script>
alert("Job's company expenses has been added uccessfully!");
window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php }
} ?>
</body>
</html>